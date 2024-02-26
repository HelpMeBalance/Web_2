# HelpMeBalance

This repository contains the Web Project

## Getting Started

### Prerequisites

Before you begin, ensure you have met the following requirements:
- [Symfony](https://symfony.com/download) installed on your local development environment.
- Basic knowledge of Symfony and Twig templating.

### No Need For Migration 

its just one command :
   ```shell
   php bin/console doctrine:schema:update --force
   ```
This will update your schema

### Installation

1. Clone this repository to your local machine using:
   ```shell
   git clone https://github.com/HelpMeBalance/Web_2/Web_2.git
   ```
2. Change into the project directory:
   ```shell
   cd Web_2
   ```
3. Install dependencies:
   ```shell
   composer install
   ```
4. Run the Symfony development server:
   ```shell
   symfony server:start
   ```
5. Access the application in your web browser at `http://localhost:8000`.


## Usage

- Explore the templates in the `templates/` directory.
- Review the Symfony routes and controller actions in your Symfony application to see how templates are rendered.
- Customize and experiment with Twig templates to enhance your understanding.

## Contributing

Contributions are welcome! Feel free to open issues or pull requests for any improvements or fixes.

