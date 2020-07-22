<?php
declare(strict_types=1);

namespace HomeSensors;


class UserUtils {

    /**
     * @throws InvalidPasswordException
     * @throws InvalidEmailException
     */
    public static function editUserFromPostData(int $id, bool $canChangeAdminStatus): void {
        $pdo = DatabaseUtils::connect();
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? '';
        $confirmPassword = $_POST["confirm_password"] ?? '';
        $isAdm = $_POST["is_admin"] ?? null;
        $isAdmin = is_string($isAdm) && strcasecmp($isAdm, "ON") === 0;

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmailException('Not a valid email');
        }
        if ($password !== '' || $confirmPassword !== '') {
            if (strlen($password) < 6) {
                throw new InvalidPasswordException('password must be at least of six characters');
            } elseif ($password !== $confirmPassword) {
                throw new InvalidPasswordException('the two passwords must match');
            }
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE User SET name=?, email=? WHERE id=?");
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $email);
            $stmt->bindValue(3, $id);
            $stmt->execute();

            if ($password !== '') {
                $stmt = $pdo->prepare("UPDATE User SET password=?, passwordIteration=passwordIteration+1 WHERE id=?");
                $stmt->bindValue(1, password_hash($password, PASSWORD_DEFAULT));
                $stmt->bindValue(2, $id);
                $stmt->execute();
            }

            if ($id !== 1 && $canChangeAdminStatus) {
                $stmt = $pdo->prepare("UPDATE User SET isAdmin=?+0 WHERE id=?");
                $stmt->bindValue(1, $isAdmin ? 1 : 0);
                $stmt->bindValue(2, $id);
                $stmt->execute();
            }
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

}