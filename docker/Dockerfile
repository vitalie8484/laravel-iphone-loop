FROM mcr.microsoft.com/playwright:v1.50.0-noble

# Install PHP 8.3 CLI + Composer + Git + Python3
RUN apt-get update && apt-get install -y \
    php-cli php-mbstring php-xml php-curl php-zip php-sqlite3 \
    composer git curl python3 python3-pip \
    && rm -rf /var/lib/apt/lists/*

# Install Python OpenAI SDK (used by our Hermes-style Worker/Checker harness to call Codex & DeepSeek)
RUN pip3 install --break-system-packages openai pillow

WORKDIR /app
