# Installation

If you are using _Git_ already in your theme, you need to clone Themalizer as a submodule using the following command:

```bash
git submodule add https://github.com/estephanbe/Themalizer.git
```

And don't forget to ignore Themalizer directory in .gitignore file.

If you are not using git in your theme, so you need just to clone Themalizer using the simple git clone method as following:

```bash
git clone https://github.com/estephanbe/Themalizer.git
```

Use the following code if the submodule was not installed

```bash
git submodule update --init --recursive
```

# Initialization

To start using Themalizer, you have to require_once it in functions.php at the top of your code as following:

```php
require_once 'Themalizer/autoload.php';
```

Please make sure to copy and edit the .env file when you move to the production

For securing your theme, go ahead and copy the following index.php file into your theme directory and **any directory** in your theme.

```markdown
.
+-- Themalizer
| +-- index.php
```
