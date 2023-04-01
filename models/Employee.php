<?php

class Employee
{
    public static string $table = "employee";

    public ?int $employee_id;
    public ?string $name;
    public ?string $surname;
    public ?int $wage;
    public ?int $room;
    public ?string $job;
    public ?string $login;
    public ?string $password;
    public ?int $admin;

    public function __construct(array $rawData = [])
    {
        $this->hydrate($rawData);
    }

    private function hydrate(array $rawData): void
    {
        if (array_key_exists('employee_id', $rawData)) {
            $this->employee_id = $rawData['employee_id'];
        }

        if (array_key_exists('name', $rawData)) {
            $this->name = $rawData['name'];
        }
        if (array_key_exists('surname', $rawData)) {
            $this->surname = $rawData['surname'];
        }
        if (array_key_exists('wage', $rawData)) {
            $this->wage = $rawData['wage'];
        }
        if (array_key_exists('room', $rawData)) {
            $this->room = $rawData['room'];
        }
        if (array_key_exists('job', $rawData)) {
            $this->job = $rawData['job'];
        }
        if (array_key_exists('login', $rawData)) {
            $this->login = $rawData['login'];
        }
        if (array_key_exists('password', $rawData)) {
            $this->password = $rawData['password'];
        }
        if (array_key_exists('admin', $rawData)) {
            $this->admin = $rawData['admin'];
        }
    }

    public static function all(array $sort = []): array
    {
        $pdo = PDOProvider::get();

        $query = "SELECT * FROM `" . self::$table . "` " . self::sortSQL($sort);
        $stmt = $pdo->query($query);

        $result = [];
        while ($employee = $stmt->fetch(PDO::FETCH_ASSOC))
            $result[] = new Employee($employee);

        return $result;
    }

    private static function sortSQL(array $sort): string
    {
        if (!$sort)
            return "";

        $sqlChunks = [];
        foreach ($sort as $column => $direction) {
            $sqlChunks[] = "`$column` $direction";
        }
        return "ORDER BY " . implode(" ", $sqlChunks);
    }

    public static function deleteById(int $employeeId): bool
    {
        $query = "DELETE FROM `".self::$table."` WHERE `employee_id` = :employeeId";

        $pdo = PDOProvider::get();

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'employeeId' => $employeeId,
        ]);
    }

    public static function readPost() : Employee
    {
        $employee = new Employee();

        $employee->employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $employee->name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
        $employee->surname = filter_input(INPUT_POST, 'surname', FILTER_DEFAULT);
        $employee->wage = filter_input(INPUT_POST, 'wage', FILTER_VALIDATE_INT);
        $employee->room = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $employee->job = filter_input(INPUT_POST, 'job', FILTER_DEFAULT);

        return $employee;
    }

    public static function findByID(int $id) : Employee|null
    {
        $pdo = PDOProvider::get();
        $query = "SELECT * FROM `" . self::$table . "` WHERE `employee_id` = $id";
        $stmt = $pdo->query($query);

        if ($stmt->rowCount() < 1)
            return null;

        return new Employee($stmt->fetch(PDO::FETCH_ASSOC));
    }


    public function validate(array &$errors = []) : bool
    {
        if (is_string($this->name))
            $this->name = trim($this->name);
        if (!$this->name)
            $errors['name'] = "Jméno musí být zadáno";

        if (is_string($this->surname))
            $this->surname = trim($this->surname);
        if (!$this->surname)
            $errors['surname'] = "Příjmení musí být zadáno";

        if ($this->wage === null)
            $errors['wage'] = "Plat musí být zadán";
        else if ($this->wage < 0)
            $errors['wage'] = "Plat musí být kladné číslo";

        if (!$this->room)
            $errors['room_id'] = "Místnost musí být zadána";

        if (!$this->job)
            $errors['job'] = "Pracovní pozice musí být zadána";

        return count($errors) === 0;
    }


    public function insert() : bool {
        $pdo = PDOProvider::get();
        $query = "INSERT INTO `" . self::$table . "` (`name`, `surname`, `wage`, `room`, `job`, `login`, `password`, `admin`) VALUES (:name, :surname, :wage, :room, :job, :login, :password, :admin)";

        if (!isset($this->login))
            $this->login = null;
        if (!isset($this->password))
            $this->password = null;
        if (!isset($this->admin))
            $this->admin = null;

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'name' => $this->name,
            'surname' => $this->surname,
            'wage' => $this->wage,
            'room' => $this->room,
            'job' => $this->job,
            'login' => $this->login,
            'password' => $this->password,
            'admin' => $this->admin
        ]);
    }

    public function update(): bool {
        $pdo = PDOProvider::get();
        $query = "UPDATE `" . self::$table . "` SET `name` = :name, `surname` = :surname, `wage` = :wage, `room` = :room, `job` = :job, `login` = :login, `password` = :password, `admin` = :admin WHERE `employee_id` = :employee_id";

        if (!isset($this->login))
            $this->login = null;
        if (!isset($this->password))
            $this->password = null;
        if (!isset($this->admin))
            $this->admin = null;

        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'surname' => $this->surname,
            'wage' => $this->wage,
            'room' => $this->room,
            'job' => $this->job,
            'login' => $this->login,
            'password' => $this->password,
            'admin' => $this->admin
        ]);
    }
}