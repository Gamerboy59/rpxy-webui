# rpxy-webui: A Management Webinterface for rust-rpxy

**rpxy-webui** is a web-based management interface designed for the [rust-rpxy](https://github.com/junkurihara/rust-rpxy) project. This interface provides an easy and user-friendly way to manage and configure your proxy servers.

## Project Overview

The rpxy-webui project aims to offer a comprehensive, intuitive, and responsive web interface for managing rust-rpxy instances. It is built using the Laravel framework, with a Bootstrap 5 frontend.

## Features

- **Dashboard**: Overview of all managed proxies and their status.
- **Proxy Management**: Add, edit, and delete proxies.
- **Upstream Management**: Configure upstreams for each proxy.
- **Settings Management**: Configure global settings with validations to ensure consistency and correctness.

### Screenshots

#### Dashboard
![Dashboard Screenshot](https://github.com/Gamerboy59/rpxy-webui/assets/1812977/8ff9f855-f8e2-4fd8-93f2-a4f84d2d7b21)

#### Upstream Overview
![Upstream Overview Screenshot](https://github.com/Gamerboy59/rpxy-webui/assets/1812977/a8aea3a9-16c1-428f-9a1a-9845ba66071a)

#### Edit Upstream
![Edit Upstream Screenshot](https://github.com/Gamerboy59/rpxy-webui/assets/1812977/58738fc3-3f37-4769-ab1f-209aba490bb2)

#### Edit Rpxy Settings
![Edit Rpxy Settings Screenshot](https://github.com/Gamerboy59/rpxy-webui/assets/1812977/b7761df4-d045-4a20-b75c-6f6ecfb9cc7d)



## Installation

To install and run rpxy-webui, follow these steps:

### Prerequisites

- **PHP**: >= 8.2
- **Composer**: Dependency Manager for PHP
- **Node.js**: >= 18.x and npm
- **MySQL**: >= 5.7

### Step-by-Step Guide

1. **Clone the Repository**

   ```bash
   git clone https://github.com/Gamerboy59/rpxy-webui.git
   cd rpxy-webui
   ```
2. **Install PHP Dependencies**
   ```bash
   composer install --no-dev
   ```

3. **Install Node Dependencies**
   ```bash
   npm install
   npm run build

   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   ```
   Update at least the following environment variables in the .env file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=localhost
   #DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations and seed database**
   ```bash
   php artisan migrate --seed
   ```
   
7. **Start rpxy server**
   ```bash
   ./target/release/rpxy -w -c /var/www/html/rpxy-webui/storage/app/config.toml
   ```
   You should preferably run rpxy in a background process or service unit.

8. **Start the Development Server** (optional)
   ```bash
   php artisan serve
   ```
   Make sure to only expose the /public/ path of your laravel webapp to the internet.

## Usage

- **Dashboard**: View a summary of all proxies and their status.
- **Proxy Management**: Add, edit, and delete proxies.
- **Upstream Management**: Add, edit, and delete upstreams for each proxy.
- **Settings Management**: Configure global settings with validation.

## Contribution

We welcome contributions to enhance the functionality and user experience of rpxy-webui. Feel free to open issues or submit pull requests for any bugs or new features you would like to see.

## License

This project is licensed under the GNU General Public License v3.0 License. See the [LICENSE](LICENSE) file for details.

## Acknowledgements

- [Laravel](https://laravel.com/)
- [Bootstrap](https://getbootstrap.com/)
- [rust-rpxy](https://github.com/junkurihara/rust-rpxy)

For more information on rust-rpxy, visit the [official repository](https://github.com/junkurihara/rust-rpxy).

---

Enjoy managing your rust-rpxy proxies with rpxy-webui!
