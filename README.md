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



## Installation and Update

There are various ways to install `rpxy-webui` and update it. Each has its pros and cons but you can switch installation type with just minimal effort by copying the `database.sqlite` and `.env` file to the new installation.

### Distribution Package

The most easy way of using rpxy is to install the package manager version. It works just like any other system package and can be automatically updated. Also, this doesn't require the installation of an additional abstraction layer. The only requirement right now is that it's only compatible to apache24 but we look into adding more later or make this optional.

[Installation Package](https://github.com/Gamerboy59/rpxy-webui/wiki/Installation-and-Update#distribution-package)

### Container

You can install `rpxy-webui` as standalone container or in combination with `rpxy` as a separate container. The latter is recommended if you have already setup a container. However, this comes with some limitations. The `rpxy` container needs to run in host-network mode and should not be switched outside the ip range to continue working with full configuration set.

[Installation Container](https://github.com/Gamerboy59/rpxy-webui/wiki/Installation-and-Update#container)

### Shared Hosting

You can run `rpxy-webui` on allmost all shared hostings. This is cost effective and can save you some management effort. However, you'll need to make the generated config file available to your local `rpxy` instance.

[Installation (pre-compiled)](https://github.com/Gamerboy59/rpxy-webui/wiki/Installation-and-Update#installation-pre-compiled)  
[Installation (self-compile)](https://github.com/Gamerboy59/rpxy-webui/wiki/Installation-and-Update#installation-self-compile)

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
