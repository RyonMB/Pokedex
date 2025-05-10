# Pokedex API

A Laravel-based backend application for managing and enriching Pokémon data, integrating with the public PokeAPI and providing a robust, secure, and extensible API for authenticated users.

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
  - Interactive OpenAPI/Swagger documentation (see `/docs` or Stoplight UI).

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

## Setup & Installation

1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd pokedex-api
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

## Contributing

Pull requests and issues are welcome! Please follow PSR-12 and Laravel conventions.

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
