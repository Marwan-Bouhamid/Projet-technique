### Difference between Session 2 and Session 3

* **Session 2 (Prototype / Client-Side Mock):**
  * Focused on **Conception and Prototyping** (e.g., class diagrams, static HTML/JS layouts).
  * Data handling was entirely client-side or mocked (storing state locally in frontend arrays or dummy data).
  * No actual backend persistence or server database (JSON / PHP API) was connected for live CRUD operations.

* **Session 3 (Live Coding / Full-Stack Integration):**
  * Focuses on building a **working Full-Stack architecture** following MVC (Model-View-Controller) principles.
  * Introduces a **PHP backend API** (`backend/api/api.php`), Controllers (`ControllerGroupeMusculaire.php`), Entity classes (`GroupeMusculaire.php`), and Manager/DAO classes (`GestionGroupeMusculaire.php`).
  * Implements **Server Persistence** using JSON files (`groupes_musculaires.json`) on the server.
  * Connects the frontend to the backend via `fetch()` requests (AJAX) to dynamically fetch and persist data (listing and adding items via forms with loading states and toast notifications).

---

### Steps to go from Session 2 to Session 3

1. **Design the Architecture (MVC):**
   * Structure your backend folders: `backend/classes/`, `backend/controller/`, `backend/api/`, and `backend/data/`.
2. **Create the Domain Model / Entity:**
   * Write the PHP Entity class (e.g., `GroupeMusculaire.php`) with private properties (`id`, `nom`, `description`), constructor, and getters.
3. **Implement Data Access / Manager Layer:**
   * Create a management class (e.g., `GestionGroupeMusculaire.php`) that reads from and writes to a JSON data file (`groupes_musculaires.json`) using `file_get_contents()` and `file_put_contents()`.
4. **Create the Controller:**
   * Build a controller class (e.g., `ControllerGroupeMusculaire.php`) that acts as an intermediary between the API endpoints and the manager methods (handling listing and adding logic).
5. **Expose the API Endpoints:**
   * Create `backend/api/api.php` to handle incoming HTTP methods (`GET` for fetching items, `POST` for adding items) and output JSON responses (`header("Content-Type: application/json")`).
6. **Connect the Frontend (`assets/js/app.js` & `index.html`):**
   * Replace static dummy data with `fetch()` calls to your PHP API.
   * Add event listeners for form submission (`POST`) and table population (`GET`).
   * Add UI enhancements like loading spinners and toast notifications.