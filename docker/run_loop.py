import os
import json
import time
import subprocess
from openai import OpenAI

MAX_PASSES = 4
TARGET_BLADE = "resources/views/welcome.blade.php"

deepseek_client = OpenAI(
    base_url="https://api.deepseek.com",
    api_key=os.environ["DEEPSEEK_API_KEY"]
)

def read_file(path):
    with open(path, "r", encoding="utf-8") as f:
        return f.read()

def write_file(path, content):
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)

def append_progress(entry):
    with open("loop_state/PROGRESS.md", "a", encoding="utf-8") as f:
        f.write(f"\n{entry}\n")

def run_worker(pass_num):
    print(f"\n[Pass {pass_num}] Running WORKER (DeepSeek Coder/Chat)...")
    contract = read_file("contract/CONTRACT.md")
    progress = read_file("loop_state/PROGRESS.md")
    features = read_file("loop_state/feature_list.json")
    current_blade = read_file(TARGET_BLADE)

    visual_spec = """
REFERENCE UI LAYOUT SPECIFICATION (from contract/layout.png):
1. Background: Deep dark slate/navy vertical gradient (#1e293b to #0f172a), white/light-slate sans-serif typography, centered mobile container (max-w-[390px] mx-auto px-4 py-8).
2. Top Current Weather Header (centered):
   - City: "London" (text-2xl font-medium text-white)
   - Current Temp: "8°" (text-7xl font-thin text-white my-1)
   - Condition: Cloud/rain icon + "Drizzle" (text-base text-slate-200)
   - High/Low: "H:8° L:5°" (text-sm text-slate-300 mt-1)
3. Middle Card — "HOURLY FORECAST":
   - Rounded-2xl semi-transparent dark card (bg-slate-800/60 backdrop-blur p-4 mt-6)
   - Header: Clock icon + "HOURLY FORECAST" (text-xs font-semibold tracking-wider text-slate-400 uppercase pb-3 border-b border-slate-700/60)
   - Horizontal row (flex overflow-x-auto gap-5 pt-3): columns for Now (8°), 1am (8°), 2am (7°), 3am (7°), 4am (6°), 5am (6°), 6am (5°), 7am (5°), 8am (5°), 9am (5°), 10am (6°), each with time on top, weather SVG icon in middle, and temp below.
4. Bottom Card — "10-DAY FORECAST":
   - Rounded-2xl semi-transparent dark card (bg-slate-800/60 backdrop-blur p-4 mt-4)
   - Header: Calendar icon + "10-DAY FORECAST" (text-xs font-semibold tracking-wider text-slate-400 uppercase pb-2 border-b border-slate-700/60)
   - 10 rows separated by subtle border-b border-slate-700/50, each row having: Day name (w-14 font-medium), weather SVG icon (w-8), low temp (w-8 text-slate-400), horizontal dark track bar (flex-1 h-1.5 bg-slate-700 rounded-full relative) with a cyan-to-green gradient range pill inside, and high temp (w-8 text-right font-medium):
     * Today: 5° to 8°
     * Tue: 6° to 13°
     * Wed: 9° to 12°
     * Thu: 6° to 9°
     * Fri: 7° to 10°
     * Sat: 6° to 13°
     * Sun: 5° to 12°
     * Mon: 2° to 7°
     * Tue: 0° to 4°
     * Wed: 1° to 5°
"""

    prompt = (
        f"You are a senior Laravel & Tailwind CSS frontend engineer.\n"
        f"Update {TARGET_BLADE} so that it matches the visual specification and satisfies every rule in CONTRACT.md.\n\n"
        f"{visual_spec}\n\n"
        f"### CONTRACT.md\n{contract}\n\n"
        f"### FEATURE LIST\n{features}\n\n"
        f"### PROGRESS LOG (What was tried previously)\n{progress}\n\n"
        f"### CURRENT {TARGET_BLADE}\n{current_blade}\n\n"
        f"### INSTRUCTIONS\n"
        f"- Output ONLY the complete, valid HTML/Blade code for {TARGET_BLADE} (no markdown fences, no commentary).\n"
        f'- Include Tailwind CSS via CDN (<script src="https://cdn.tailwindcss.com"></script>) so styles render immediately.\n'
        f"- Ensure <body> itself has a dark slate background color (e.g. style=\"background-color: #162032;\") so computed background checks pass.\n"
        f"- Ensure zero horizontal page overflow at 390px viewport width.\n"
    )

    response = deepseek_client.chat.completions.create(
        model="deepseek-chat",
        messages=[{"role": "user", "content": prompt}],
        temperature=0.3
    )

    code = response.choices[0].message.content.strip()
    if code.startswith("```"):
        lines = code.splitlines()
        code = "\n".join(lines[1:-1] if lines[-1].startswith("```") else lines[1:])

    write_file(TARGET_BLADE, code)
    subprocess.run(["git", "add", TARGET_BLADE], check=True)
    subprocess.run(["git", "commit", "-m", f"feat(loop): pass {pass_num} worker update", "--allow-empty"], check=True)
    print(f"[Pass {pass_num}] Worker committed changes to {TARGET_BLADE}.")

def run_verifier(pass_num):
    print(f"[Pass {pass_num}] Running DETERMINISTIC VERIFIER (Playwright -> {os.environ.get('APP_URL', 'http://laravel-iphone-loop.test/')})...")
    if os.path.exists("artifacts/verify_report.json"):
        os.remove("artifacts/verify_report.json")
    result = subprocess.run(["node", "contract/verify_layout.js"], capture_output=True, text=True)
    if result.stdout:
        print(result.stdout)
    if result.stderr:
        print("VERIFIER STDERR:", result.stderr)
    return result.returncode == 0

def run_checker(pass_num, verifier_passed):
    print(f"[Pass {pass_num}] Running CHECKER (Adversarial QA Persona)...")
    contract = read_file("contract/CONTRACT.md")
    verify_report = read_file("artifacts/verify_report.json")
    git_diff = subprocess.check_output(["git", "diff", "HEAD~1", "HEAD", "--", TARGET_BLADE]).decode("utf-8")

    prompt = (
        f"You are a strict, skeptical QA Checker grading a Worker code diff against CONTRACT.md.\n"
        f"Do NOT trust the Worker; rely only on the Playwright report and Git diff.\n\n"
        f"### CONTRACT.md\n{contract}\n\n"
        f"### PLAYWRIGHT VERIFIER REPORT (artifacts/verify_report.json)\n{verify_report}\n\n"
        f"### GIT DIFF OF {TARGET_BLADE}\n{git_diff[:6000]}\n\n"
        f"### DECISION RULES\n"
        f"1. If passed is false in verify_report.json, you MUST reject the work (status: RETRY) and list the exact errors to fix.\n"
        f"2. Verify that all 3 weather sections (Current Weather Header, Hourly Forecast, 10-Day Forecast with 10 rows and range bars) are authentically implemented.\n"
        f"3. Respond in strict JSON format with keys status (DONE, RETRY, or IMPOSSIBLE) and notes.\n"
    )

    response = deepseek_client.chat.completions.create(
        model="deepseek-chat",
        messages=[{"role": "user", "content": prompt}],
        response_format={"type": "json_object"},
        temperature=0.0
    )

    decision = json.loads(response.choices[0].message.content)
    print(f"[Pass {pass_num}] Checker Verdict: {decision['status']} — {decision['notes']}")
    return decision

def main():
    subprocess.run(["git", "config", "user.email", "loop-worker@local"], check=True)
    subprocess.run(["git", "config", "user.name", "Hermes Loop Worker"], check=True)

    for pass_num in range(1, MAX_PASSES + 1):
        print(f"\n{'='*50}\nSTARTING LOOP PASS {pass_num} OF {MAX_PASSES}\n{'='*50}")
        run_worker(pass_num)
        verifier_passed = run_verifier(pass_num)
        decision = run_checker(pass_num, verifier_passed)

        status = decision.get("status", "RETRY").upper()
        notes = decision.get("notes", "")

        append_progress(f"- **Pass {pass_num} ({status}):** {notes}")

        if status == "DONE" and verifier_passed:
            features = json.loads(read_file("loop_state/feature_list.json"))
            for item in features:
                item["status"] = "completed"
            write_file("loop_state/feature_list.json", json.dumps(features, indent=2))
            print("\nLOOP EXIT: DONE! Contract verified and approved.")
            return

        if status == "IMPOSSIBLE":
            print("\nLOOP EXIT: IMPOSSIBLE. Halting execution.")
            return

    print(f"\nLOOP EXIT: NEEDS_PERSON (Reached hard cap of {MAX_PASSES} passes).")

if __name__ == "__main__":
    main()
