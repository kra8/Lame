# Lame
Laravel + Homestead の環境を整える

## Version 1.1.0
### Adaptコマンドを作成
Adaptコマンドは既存のLaravelプロジェクトに対してHomesteadを適応することができます。

ex.
```
git clone <repository_path>
lame adapt <repository_name>
```

## Usage
* `lame new <project_name>`  
project_nameという新規プロジェクトを作成する。

* `lame adapt <project_name>`  
既存のLaravelプロジェクトに対してHomesteadを適応する。

## Install
```
composer global require "kra8/lame"
```

インストールしたら、`$HOME/.composer/vendor/bin`にパスを通してください。

## Require
LaravelとHomesteadを動かすには、以下のソフトウェアをインストールしておく必要があります。

* PHP 7.1.3以上
* VirtualBox
* Vagrant

## License
MIT
