import os
import sys
import json
import subprocess
from openai import OpenAI

pass_num = int(sys.argv[1]) if len(sys.argv) > 1 else 1
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

def main():
    print(f"[Pass {pass_num}] Running DETERMINISTIC VERIFIER (Playwright -> {os.environ.get('APP_URL')})...")
    if os.path.exists("artifacts/verify_report.json"):
        os.remove("artifacts/verify_report.json")

    res = subprocess.run(["node", "contract/verify_layout.js"], capture_output=True, text=True)
    if res.stdout:
        print(res.stdout)
    if res.stderr:
        print("VERIFIER STDERR:", res.stderr)

    verifier_passed = (res.returncode == 0)
    verify_report = read_file("artifacts/verify_report.json") if os.path.exists("artifacts/verify_report.json") else '{"passed": false, "errors": ["Verifier crashed"]}'
    contract = read_file("contract/CONTRACT.md")
    git_diff = subprocess.check_output(["git", "diff", "HEAD~1", "HEAD", "--", TARGET_BLADE]).decode("utf-8")

    print(f"[Pass {pass_num}] Running CHECKER (DeepSeek)...")
    prompt = (
        f"You are an adversarial QA Checker grading a Codex Worker diff against CONTRACT.md.\n"
        f"Do NOT trust the Worker; rely only on the Playwright report and Git diff.\n\n"
        f"### CONTRACT.md\n{contract}\n\n"
        f"### PLAYWRIGHT REPORT\n{verify_report}\n\n"
        f"### GIT DIFF OF {TARGET_BLADE}\n{git_diff[:6000]}\n\n"
        f"### RULES\n"
        f"1. If passed is false in the Playwright report, respond with status: RETRY and exact errors.\n"
        f"2. Confirm all 3 weather sections (header, hourly forecast with moons/sun/clouds, 10-day forecast with range bars) are authentically implemented.\n"
        f"3. Respond strictly in JSON with keys: status (DONE, RETRY, or IMPOSSIBLE) and notes.\n"
    )

    response = deepseek_client.chat.completions.create(
        model="deepseek-chat",
        messages=[{"role": "user", "content": prompt}],
        response_format={"type": "json_object"},
        temperature=0.0
    )

    decision = json.loads(response.choices[0].message.content)
    status = decision.get("status", "RETRY").upper()
    notes = decision.get("notes", "")
    print(f"[Pass {pass_num}] Checker Verdict: {status} — {notes}")

    append_progress(f"- **Pass {pass_num} ({status}):** {notes}")

    if status == "DONE" and verifier_passed:
        features = json.loads(read_file("loop_state/feature_list.json"))
        for item in features:
            item["status"] = "completed"
        write_file("loop_state/feature_list.json", json.dumps(features, indent=2))
        sys.exit(0)

    sys.exit(1)

if __name__ == "__main__":
    main()
