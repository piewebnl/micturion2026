# AI Coding Agent Instructions

This Laravel application is a music collection management system integrating multiple external APIs (Spotify, Discogs, Last.fm, iTunes Library). Use these guidelines to work productively in this codebase.

## Project Architecture

**Core Tech Stack:**
- Laravel 12 with Filament 4 admin panel
- Livewire 3 + Volt for reactive UI components
- PHP 8.2+ with strict type declarations
- SQLite (testing) / database agnostic migrations

**Domain Structure:**
The app manages multiple interconnected music sources and user data:
- **Spotify Integration**: Album/track search, status tracking via `AlbumSpotifyAlbum`, `SongSpotifyTrack` models
- **Discogs Integration**: Collection importing and release matching (see `app/Services/Discogs/`)
- **iTunes Library**: Extra tracks CSV import via `ItunesLibraryExtraTracksImporter`
- **Last.fm API**: User stats and listening data
- **Music Core**: Albums, songs, and user wishlist management

**Service Layer Pattern:**
Services handle business logic and API interactions. Organize by domain with clear responsibility boundaries:
- `app/Services/DiscogsApi/Getters/` - API retrieval with pagination
- `app/Services/Discogs/Importers/` - Data transformation and storage
- `app/Services/Discogs/Matchers/` - Matching logic between datasets
- `app/Services/ItunesLibrary/ItunesLibraryExtraTracksImporter` - File parsing and bulk import

Services return `JsonResponse` via `response()->success()` / `response()->error()` for API compatibility.

## Critical Workflows & Commands

**Development Startup:**
```bash
composer run dev
```
Runs concurrently: Laravel server, queue worker, logging, and Vite bundler. Essential for local development.

**Database & Testing:**
```bash
php artisan test                    # Run all tests
php artisan migrate --force         # Apply migrations
php artisan tinker                  # Interactive PHP shell
```

**Frontend Build:**
If UI changes don't appear, rebuild:
```bash
npm run build    # Production build
npm run dev      # Development with watch
```

**Debugging:**
- Laravel Debugbar available at bottom of pages (dev environment)
- Log viewer: `php artisan log:viewer` command (OpCodesIO package)
- Check `storage/logs/` for application logs

## Code Patterns & Conventions

**Models & Relationships:**
- Use descriptive property names: `$isRegisteredForDiscounts` not `$discount`
- Type-hint all properties and method returns
- Define `$fillable` arrays explicitly
- Use Laravel query caching trait: `app/Traits/QueryCache/QueryCache.php`

**Service Classes:**
- Single responsibility - one service per feature/API
- Constructor injection for dependencies
- Return `JsonResponse` with structured data for APIs
- Private methods for internal logic, public for entry points
- Example: `ItunesLibraryExtraTracksImporter::import()` handles validation and response formatting

**Livewire Components:**
- Located in `app/Livewire/` organized by domain (Spotify, Wishlist, Discogs, etc.)
- Use traits from `app/Livewire/Forms/` for reusable form logic
- Boot method initializes component state
- Dispatch events via `$this->dispatch()` for parent-child communication
- Example: `WishlistSearch` uses `SearchForm` trait with filter initialization

**Traits (Reusable Behavior):**
- **Converters**: Transform API data to internal models (`ToSpotifyTrackConverter`, `ToDiscogsReleaseConverter`)
- **Renamers**: Normalize names across APIs (`ToSpotifySearchTrackRenamer`)
- **QueryCache**: Cache complex queries to reduce API calls
- **Forms**: Shared form initialization and filter logic

**Response Format Convention:**
Services use Laravel response helper with custom methods:
```php
$this->response = response()->success('Import complete', ['tracks' => $data]);
$this->response = response()->error('Failed to parse file');
```

## Important Developer Notes

**No Custom Response Macros - Check Existing:**
The `response()->success()` and `response()->error()` helpers are defined globally. Always verify existing implementations before creating new ones.

**Service Initialization Pattern:**
Services initialize in constructor then expose public methods. State persists for getters:
```php
$importer = new ItunesLibraryExtraTracksImporter($csvPath);
$importer->import();
$response = $importer->getResponse();
```

**Avoid Over-Abstraction:**
Keep service methods focused. Use single-purpose classes over god objects. Compare with existing `Discogs/` folder structure.

**Config-Driven APIs:**
External API credentials and URLs stored in `config/` - check `spotify.php`, `discogs.php`, `lastfm.php`, `ituneslibrary.php` before hardcoding values.

**Testing Infrastructure:**
- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- Base `TestCase` extends Laravel's testing framework
- Run full suite: `composer run test`

## File Location Cheat Sheet

| Pattern | Location | Example |
|---------|----------|---------|
| API Integration | `app/Services/{Domain}Api/` | `DiscogsApi/Getters/` |
| Business Logic | `app/Services/{Domain}/` | `Discogs/Importers/` |
| Database Models | `app/Models/` | `Album.php`, `AlbumSpotifyAlbum.php` |
| UI Components | `app/Livewire/{Domain}/` | `Wishlist/WishlistSearch.php` |
| Reusable Logic | `app/Traits/{Category}/` | `Traits/Converters/` |
| Admin Panels | `app/Filament/Resources/` | Filament CRUD resources |
| Background Jobs | `app/Jobs/{Domain}/` | iTunes/Spotify sync jobs |
| API Routes | `routes/api.php` | JSON endpoints |
| Web Routes | `routes/web.php` | Volt component routes |

## Before Starting Any Task

1. **Check sibling files** - Review similar implementations for naming and structure conventions
2. **Verify the model** - Inspect `app/Models/` to understand data relationships
3. **Look at existing services** - Is there already a similar service? Extend or follow its pattern
4. **Review config files** - External API keys and URLs are environment-configurable
5. **Check migrations** - Understand schema before writing queries

## Questions to Debug Issues

- Is the queue running? Check `composer run dev` output
- Did you rebuild frontend? Run `npm run build`
- Are models properly related? Check Laravel relationship definitions
- Is API rate-limited? Check service logs in `storage/logs/`
- Database issues? Use `php artisan tinker` to test queries
