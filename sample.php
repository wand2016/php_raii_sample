<?php

class FileContainer
{
    private $handle;

    public function __construct(string $filename, string $mode)
    {
        $this->handle = fopen($filename, $mode);
        if (!$this->acquired()) {
            throw new \Exception("couldn't acquire resource\n");
        }
        echo "acquired\n";
    }

    public function __destruct()
    {
        if ($this->acquired()) {
            fclose($this->handle);
            echo "released\n";
        }
    }

    public static function create(string $filename, string $mode): self
    {
        return new self($filename, $mode);
    }

    public function read(int $length)
    {
        return fread($this->handle, $length);
    }

    /**
     * カプセル化を破るけど汎用的なやつ
     */
    public function run(callable $callback): void
    {
        $callback($this->handle);
    }

    private function acquired(): bool
    {
        return $this->handle !== false;
    }
}

function doSomething(): void
{
    $fileContainer = FileContainer::create('./stub.txt', 'r');

    $content = $fileContainer->read(1024);

    echo $content;
}

function doSomething2(): void
{
    $fileContainer = FileContainer::create('./stub.txt', 'r');

    $fileContainer->run(function ($handle) {
        $content = fread($handle, 1024);
        echo $content;
    });
}

function doSomething3(): void
{
    $fileContainer = FileContainer::create('./stub.txt', 'r');

    $content = $fileContainer->read(1024);

    throw new \Exception('whoops');

    echo $content;
}


doSomething();
doSomething2();
doSomething3();
