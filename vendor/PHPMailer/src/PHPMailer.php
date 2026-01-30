<?php

namespace PHPMailer\PHPMailer;

class PHPMailer
{
    public string $Host = '';
    public int $Port = 587;
    public bool $SMTPAuth = true;
    public string $Username = '';
    public string $Password = '';
    public string $SMTPSecure = '';
    public string $Subject = '';
    public string $Body = '';
    public string $AltBody = '';
    public string $ErrorInfo = '';

    private string $fromEmail = '';
    private string $fromName = '';
    private array $addresses = [];
    private array $replyTo = [];
    private bool $isHtml = false;
    private array $attachments = [];

    public function __construct(bool $exceptions = false)
    {
    }

    public function isSMTP(): void
    {
    }

    public function setFrom(string $email, string $name = ''): void
    {
        $this->fromEmail = $email;
        $this->fromName = $name;
    }

    public function addAddress(string $email, string $name = ''): void
    {
        $this->addresses[] = ['email' => $email, 'name' => $name];
    }

    public function addReplyTo(string $email, string $name = ''): void
    {
        $this->replyTo[] = ['email' => $email, 'name' => $name];
    }

    public function addAttachment(string $path): void
    {
        $this->attachments[] = $path;
    }

    public function isHTML(bool $flag = true): void
    {
        $this->isHtml = $flag;
    }

    public function send(): bool
    {
        if (empty($this->addresses)) {
            $this->ErrorInfo = 'No recipient specified.';
            return false;
        }

        $to = implode(',', array_map(fn ($address) => $address['email'], $this->addresses));
        $headers = [];
        $headers[] = 'From: ' . ($this->fromName ? $this->fromName . ' <' . $this->fromEmail . '>' : $this->fromEmail);
        if (!empty($this->replyTo)) {
            $headers[] = 'Reply-To: ' . $this->replyTo[0]['email'];
        }
        if ($this->isHtml) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=UTF-8';
        }

        $result = mail($to, $this->Subject, $this->Body, implode("\r\n", $headers));
        if (!$result) {
            $this->ErrorInfo = 'mail() failed.';
        }
        return $result;
    }
}
