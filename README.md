# MeetSync — Сервис планирования встреч с подбором общего времени

> **Курсовая работа**  
> **Стек:** Laravel 13 (PHP 8.3+, Strict Types) + React 19 (Vite, TypeScript, Tailwind CSS, shadcn/ui) + PostgreSQL 16 + Redis  
> **Архитектура:** Decoupled REST API + SPA, Service-Repository Layer, Event-Driven Queues, Sweep-line Slot Algorithm.

---

## 📌 О проекте

**MeetSync** решает классическую проблему координации коллективных встреч: когда нескольким участникам сложно согласовать общее удобное время. 

### Основной сценарий:
1. **Организатор** создаёт карточку встречи: указывает тему, описание, длительность (например, 45 минут), диапазон дат и часовой пояс.
2. **Приглашение:** Система формирует защищённые ссылки и рассылает email-приглашения через асинхронную очередь.
3. **Сбор доступности:** Участники (как зарегистрированные пользователи, так и гости по токену) в интерактивном визуальном календаре отмечают интервалы свободного времени.
4. **Алгоритмический расчёт:** Серверный сервис на базе алгоритма заметающей прямой (Sweep-line) рассчитывает пересечения свободных интервалов и формирует ранжированный список рекомендованных слотов (100% явки, 80% и т.д.).
5. **Подтверждение:** Организатор выбирает финальный слот. Система фиксирует встречу, генерирует события календаря (.ics) и рассылает подтверждения и напоминания.

---

## 🏗 Архитектурные требования и стандарты (ТЗ от комиссии)

Проект спроектирован в строгом соответствии с академическими и индустриальными требованиями:
- **Services (Бизнес-логика):** 
  - `MeetingService` — управление жизненным циклом встреч.
  - `InvitationService` — генерация защищенных токенов и отправка инвайтов.
  - `AvailabilityService` — валидация и сохранение интервалов участников.
  - `SlotCalculationService` — математический расчёт общих пересечений (Sweep-line).
  - `SlotConfirmationService` — финализация встречи, генерация .ics файлов, отправка нотификаций.
- **Thin Controllers:** Контроллеры не содержат бизнес-логики; они валидируют вход через `FormRequest`, передают DTO в соответствующий Service и возвращают типизированный `JsonResponse` / `JsonResource`.
- **Repositories:** Изолируют слой работы с Eloquent ORM и базой данных (`MeetingRepository`, `AvailabilityRepository`, `ParticipantRepository`).
- **FormRequests:** Строгая серверная валидация для каждого действия (`StoreMeetingRequest`, `SubmitAvailabilityRequest`, `ConfirmSlotRequest`).
- **Middleware:** `EnsureMeetingParticipant` — проверяет права доступа к встрече (авторизованный пользователь или валидный UUID-токен приглашения).
- **Queues (Асинхронные очереди):** Рассылка приглашений, напоминаний за 24ч/1ч до встречи через Redis + Laravel Horizon.
- **Strict Typing:** `declare(strict_types=1);` в каждом PHP-файле, Enums для статусов, DTO для передачи данных, уровень статического анализа Larastan 8+.

---

## 🛠 Технологический стек

### Backend:
- **PHP 8.3+** с включённым strict_types
- **Laravel 13** (REST API)
- **PostgreSQL 16** (хранение интервалов, таймзон и статусов)
- **Redis 7** (кэширование, сессии, очереди)
- **Laravel Horizon** (панель мониторинга очередей)
- **Laravel Sanctum** (аутентификация организаторов)
- **Pest PHP** (модульные и интеграционные тесты)
- **Larastan** (PHPStan Level 8)

### Frontend:
- **React 19 + TypeScript (Strict Mode)**
- **Vite** (сборщик)
- **Tailwind CSS v3/v4** + **shadcn/ui** (доступные UI-примитивы на Radix UI)
- **TanStack Query v5** (кэширование серверного состояния, оптимистичные апдейты)
- **Lucide Icons**
- **Vitest** (тестирование алгоритмов на стороне клиента)

### DevOps, SEO & Оптимизация:
- **Docker & Docker Compose** (контейнеры для локальной разработки и продакшена)
- **Nginx / Caddy** с автоматическим Let's Encrypt SSL
- **SEO & Мета:** Динамические OpenGraph/Twitter карточки для мессенджеров, `robots.txt`, `sitemap.xml`, `llms.txt`
- **Мониторинг:** Эндпоинт `/api/health`, Яндекс.Метрика с вебвизором

---

## 📂 Структура репозитория

```
CourseWork/
├── backend/                  # Laravel 13 API (PHP 8.3, Strict Types)
│   ├── AGENTS.md             # Инструкции для агентов по бэкенду
│   ├── DESIGN.md             # Спецификация архитектуры бэкенда, БД и алгоритма
│   └── ...
├── frontend/                 # React 19 SPA (Vite + TypeScript)
│   ├── AGENTS.md             # Инструкции для агентов по фронтенду
│   ├── DESIGN.md             # Спецификация UI/UX, компонентов и дизайн-системы
│   └── ...
├── docker/                   # Конфигурации Nginx, PHP-FPM, Dockerfile
├── docker-compose.yml        # Локальное окружение (PostgreSQL, Redis, Mailpit)
├── docker-compose.prod.yml   # Продакшен деплой (SSL, Opcache, Caddy/Nginx)
├── README.md                 # Этот файл
└── AGENTS.md                 # Главный навигационный хаб для AI-агентов
```

---

## 🚀 Быстрый старт (Локальная разработка)

### Требования:
- Docker & Docker Compose
- Git
- Node.js 20+ и PHP 8.3 (при локальном запуске без Docker)

### Шаги установки:

1. **Запустить инфраструктурные контейнеры (БД, Redis, Mailpit):**
   ```bash
   docker compose up -d
   ```

2. **Инициализация Backend:**
   ```bash
   cd backend
   cp .env.example .env
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan horizon:install
   php artisan serve --port=8000
   ```

3. **Инициализация Frontend:**
   ```bash
   cd ../frontend
   cp .env.example .env
   npm install
   npm run dev
   ```

4. **Доступные интерфейсы:**
   - Frontend SPA: `http://localhost:5173`
   - Backend API: `http://localhost:8000`
   - Laravel Horizon (Очереди): `http://localhost:8000/horizon`
   - Mailpit (Локальный почтовый ящик): `http://localhost:8025`

---

## 🧪 Тестирование и качество кода

```bash
# Тесты бэкенда (Pest)
cd backend
./vendor/bin/pest

# Статический анализ бэкенда (Level 8)
./vendor/bin/phpstan analyse

# Тесты и линтинг фронтенда
cd ../frontend
npm run lint
npm run test
```
