# Pokedex API

A Laravel-based backend application for managing and enriching Pokémon data, integrating with the public PokeAPI and providing a robust, secure, and extensible API for authenticated users.
I used Contracts/Interfaces to demonstrate what I'm capable of, it could be a little bit overengineered for this purpose and would not use an interface in a real life application of this size.

---

## Features

- **Fetch Pokémon Data:**
  - Retrieves Pokémon data from the public [PokeAPI](https://pokeapi.co/) (no dedicated package used).
  - Handles rate limits and errors gracefully.
- **Data Storage:**
  - Stores relevant Pokémon details in a relational database.
  - Supports user-specific enrichment (favorites, tags, comments, etc.).
- **REST API:**
  - Exposes endpoints to list, search, and retrieve Pokémon details.
  - Allows authenticated users to enrich Pokémon data.
  - Follows RESTful and Laravel best practices.
- **Batch Processing:**
  - Periodically pushes stored Pokémon data to an external API.
  - Uses efficient chunking, batching, and queueing for large datasets.
- **Filtering:**
  - Advanced, extensible filtering system for API queries (range, search, exact, relation-aware).
- **Validation:**
  - Automatic validation of filter and enrichment requests.
- **Documentation:**
  - Interactive OpenAPI/Swagger documentation (see `/docs`).
  - **API documentation is always available at [`/docs`](http://localhost:8000/docs) when running the application locally.**

---

## API Endpoints (v1)

- `GET /api/v1/pokemon` — List/filter all Pokémon in your Pokédex
- `GET /api/v1/pokemon/all` — Search all available Pokémon
- `GET /api/v1/pokemon/{name}` — Get a Pokémon by name
- `POST /api/v1/pokemon` — Add a Pokémon to your Pokédex
- `POST /api/v1/pokemon/favorite` — Mark/unmark a Pokémon as favorite
- `POST /api/v1/pokemon/detach` — Remove a Pokémon from your Pokédex

See the API documentation for full request/response details and filter options.

---

## Authentication

- Uses Laravel Sanctum for API authentication.
- Register/login to receive a token for authenticated endpoints.

---

## Batch & Queue System

- Uses Laravel's queue and batch processing for pushing Pokémon data to external APIs.
- Efficient, memory-safe processing with chunking and cursor-based iteration.
- Jobs are idempotent and robust against failures.

---

## Traits

This project makes use of PHP Traits to promote code reuse and maintainability:

- **Filter**: Adds advanced, extensible filtering capabilities to Eloquent models, supporting range, search, exact, and relation-aware filters.
- **FilterRequestTrait**: Provides dynamic validation rules and messages for filterable API requests, ensuring robust and consistent input validation.
- **InvalidatesPokemonCache**: Automatically invalidates relevant Pokémon cache entries and marks them as changed when models are updated or deleted, ensuring data consistency.

Traits are located in `app/Traits/` and are used throughout the codebase to encapsulate reusable logic.

---

## Setup & Installation

1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   ```
2. **Install dependencies:**
   ```bash
   composer install
   npm install # if using frontend assets
   ```
3. **Configure environment:**
   - Copy `.env.example` to `.env` and set your DB and queue credentials.
   - Run `php artisan key:generate`
4. **Run migrations:**
   ```bash
   php artisan migrate
   ```
5. **Start the local Development server:**
   ```
   composer dev
   ```

---

## Testing

- Run tests with:
  ```bash
  php artisan test
  ```

---

## Code Quality & Standardization

To ensure code quality and consistency, this project uses automated tools for linting and refactoring. You can run the following command to automatically fix code style issues and apply safe refactorings:

```bash
composer fix
```

This will run both Rector (for automated code refactoring) and Pint (for code style/linting) according to the project's configuration. It is recommended to run this command before committing code to maintain a clean and standardized codebase.

---

