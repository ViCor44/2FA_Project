# Sistema de Login com 2FA

Este projeto é um **sistema de login** com **opção de autenticação de dois fatores (2FA)**, desenvolvido em PHP, MySQL e estilizado com o framework **Tailwind CSS**.

## Funcionalidades

- **Autenticação de Usuário:** Sistema de login simples com verificação de nome de usuário e senha.
- **Autenticação de Dois Fatores (2FA):** 
  - Quando habilitado, o usuário será redirecionado para uma página onde deverá realizar a verificação 2FA após o login.
  - Suporte para a configuração e verificação do 2FA com a utilização de uma chave temporária (gerada com o Google Authenticator ou outros aplicativos similares).  
- **Dashboard:** Após o login bem-sucedido, o usuário é redirecionado para um painel de controle, que pode ser estendido para incluir informações relevantes para o sistema de gestão de manutenção.
- **Segurança:** Senha criptografada no banco de dados, proteção contra ataques comuns, e a autenticação de dois fatores adiciona uma camada extra de segurança.

## Requisitos

- PHP 7.4 ou superior
- MySQL
- Composer (para gerenciar dependências)
- Extensão cURL do PHP
- Biblioteca de 2FA (Google Authenticator, Authy ou similar)

## Instalação

1. **Clone o repositório**:
    ```bash
    git clone https://github.com/seu-usuario/worklog-cmms.git
    ```

2. **Instale as dependências com o Composer**:
    ```bash
    cd worklog-cmms
    composer install
    ```

3. **Crie o banco de dados** no MySQL com as tabelas necessárias para o funcionamento do sistema:
    - Crie um banco de dados chamado `worklog_cmms`.
    - Importe o esquema de banco de dados que está disponível no diretório `database/` (se aplicável).

4. **Configuração do Banco de Dados:**
    - No arquivo `config.php`, insira suas credenciais de banco de dados.

    ```php
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'seu_usuario');
    define('DB_PASSWORD', 'sua_senha');
    define('DB_NAME', 'worklog_cmms');
    ```

5. **Configuração do 2FA:**
    - Para a configuração da autenticação de dois fatores, use um aplicativo como **Google Authenticator** ou **Authy**.
    - Ao realizar o login, se o 2FA estiver habilitado, o sistema redirecionará o usuário para a página de verificação.

6. **Acesse o Sistema:**
    - Depois de configurar o banco de dados e as dependências, acesse o sistema via navegador:
    ```bash
    http://localhost/worklog-cmms
    ```

## Como Contribuir

1. **Fork o Repositório**.
2. **Crie uma nova branch** (`git checkout -b feature/nova-funcionalidade`).
3. **Faça o commit** das suas alterações (`git commit -am 'Adiciona nova funcionalidade'`).
4. **Push para a branch** (`git push origin feature/nova-funcionalidade`).
5. **Abra um Pull Request**.

## Doações

Se você gostou do projeto e deseja apoiar o desenvolvimento contínuo, você pode fazer uma doação através do botão abaixo.

[![Doe com PayPal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/donate?business=victor.a.correia@gmail.com)

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## Agradecimentos

- **Tailwind CSS** - Framework de design que foi utilizado para a estilização.
- **Google Authenticator** - Para implementação da autenticação de dois fatores.
- **PHP** - Para a lógica de backend do sistema.
- **MySQL** - Para o armazenamento de dados do usuário e configurações.

---

Se você tiver alguma dúvida ou sugestão, sinta-se à vontade para abrir uma **issue** ou contribuir com um **pull request**!
