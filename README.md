# Advanced Warranty Management Plugin

A comprehensive plugin for creating and managing a product warranty system on WordPress websites. This plugin allows administrators to manage warranties and enables authorized agents to register new warranties for customers through a simple front-end form. It also includes a public-facing search tool for anyone to check a warranty's status.

## Core Features

*   **Dedicated Database Table**: Creates a custom `warranties` table to store all warranty data and metadata efficiently, ensuring no bloat in core WordPress tables and improving query performance.
*   **Full-Featured Admin Panel**:
   *   "Add New Warranty" screen with all necessary fields.
   *   "Edit Warranty" interface to update existing records.
   *   A complete list of all registered warranties with powerful search and filtering capabilities.
*   **Agent Role Integration**: Seamlessly integrates with custom user roles (e.g., 'Agent'), granting them exclusive access to the warranty submission form.
*   **Powerful Shortcodes**:
   *   `[warranty_submission_form]`: Displays the warranty registration form. Best used on a private page accessible only to agents.
   *   `[warranty_list]`: Shows a list of warranties submitted by the currently logged-in agent.
   *   `[warranty_search_form]`: Renders a public form for any user to search for a warranty by serial number or other identifiers.
*   **Public Search Functionality**: Allows any site visitor to check their warranty status, providing transparency and reducing support requests.
*   **Developer-Friendly**: Built with WordPress coding standards, action hooks, and filters, making it extensible and easy to customize.

## Installation

1.  Upload the `advanced-warranty-management` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in your WordPress admin panel.
3.  Once activated, a new **Warranties** menu will appear in the WordPress dashboard.
4.  Create new pages and use the shortcodes `[warranty_submission_form]` and `[warranty_search_form]` to deploy the forms.

## Requirements

*   **WordPress Version:** 5.2 or higher
*   **PHP Version:** 7.4 or higher

---
**Note**: This is the initial release. Future versions will include more features and enhancements.