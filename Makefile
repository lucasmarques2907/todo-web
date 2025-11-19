.SILENT:

ifeq ($(OS),Windows_NT)
SHELL := C:/Program Files/Git/bin/bash.exe
else
SHELL := /usr/bin/env bash
endif

# ===== CONFIGURAÇÕES =====
API_URL=http://127.0.0.1:8000
DOCS_URL=http://127.0.0.1:8000/docs/api
DB_PATH=database/database.sqlite

# ===== TARGETS =====

bootstrap: env key db migrate serve

env:
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo "✔ .env criado"; \
	else \
		echo "ℹ .env já existe, pulando..."; \
	fi

key:
	php artisan key:generate --force
	@echo "✔ APP_KEY gerado"

db:
	@if [ ! -f $(DB_PATH) ]; then \
		touch $(DB_PATH); \
		echo "✔ SQLite criado em $(DB_PATH)"; \
	else \
		echo "ℹ SQLite já existe, pulando..."; \
	fi

migrate:
	php artisan migrate --force
	@echo "✔ Migrations rodadas"

serve:
	@echo ""
	@echo "🚀 Seu projeto está rodando!"
	@echo "--------------------------------"
	@echo "📌 Backend:     $(API_URL)"
	@echo "📌 Documentação: $(DOCS_URL)"
	@echo "--------------------------------"
	php artisan serve
