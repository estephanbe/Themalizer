# Installation

If you are using *Git* already in your theme, you need to clone Themalizer as a submodule using the following command:

```bash
git submodule add https://github.com/estephanbe/Themalizer.git
```

If you are not using git in your theme, so you need just to clone Themalizer using the simple git clone method as following:

```bash
git clone https://github.com/estephanbe/Themalizer.git
```



# Initialization

To start using Themalizer, you have to require_once it in functions.php at the top of your code as following:

```php
require_once 'Themalizer/autoload.php';
```



If you are in development mode, add the following line: 

```php
Themalizer::$development = true;
```

as it skips some initializations for production environment such as some security headers.



For securing your theme, go ahead and copy the following index.php file into your theme directory and **any directory** in your theme.

```markdown
.
+-- Themalizer
|	+-- index.php
```

