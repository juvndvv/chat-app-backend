<?php

namespace Tests\User\Domain\Entity;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Tests\TestCase;

final class UserTest extends TestCase
{
    public function testGetId()
    {
        $id = UserId::generate()->value();
        $user = $this->createTestUserEntity(
            id: $id
        );

        $this->assertEquals($id, $user->getId());
    }

    public function testGetIdFails()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s id is not set');

        $user = User::build();
        $user->getId();
    }

    public function testGetName()
    {
        $name = 'Juan';
        $user = $this->createTestUserEntity(
            name: $name
        );

        $this->assertEquals($name, $user->getName());
    }

    public function testGetNameFails()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s name is not set');

        $user = User::build();
        $user->getName();
    }

    public function testGetFirstLastName()
    {
        $firstLastName = 'Forner';

        $user = $this->createTestUserEntity(
            firstLastName: $firstLastName
        );

        $this->assertEquals($firstLastName, $user->getFirstLastName());
    }

    public function testGetFirstLastNameFails()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s firstLastName is not set');

        $user = User::build();
        $user->getFirstLastName();
    }

    public function testGetSecondLastName()
    {
        $secondLastName = 'Forner';

        $user = $this->createTestUserEntity(
            secondLastName: $secondLastName
        );

        $this->assertEquals($secondLastName, $user->getSecondLastName());
    }

    public function testGetSecondLastNameFails()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s secondLastName is not set');

        $user = User::build();
        $user->getSecondLastName();
    }

    public function testGetEmail()
    {
        $email = 'jdanielforga@gmail.com';

        $user = $this->createTestUserEntity(
            email: $email
        );

        $this->assertEquals($email, $user->getEmail());
    }

    public function testGetEmailFails()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s email is not set');

        $user = User::build();
        $user->getEmail();
    }

    public function testGetFullName()
    {
        $user = $this->createTestUserEntity(
            name: ' Juan Daniel ',
            firstLastName: ' Forner ',
            secondLastName: ' Garriga '
        );

        $this->assertEquals("Juan Daniel Forner Garriga", $user->getFullName());
    }

    public function testGetFullNameWithoutSecondLastName()
    {
        $user = $this->createTestUserEntity(
            name: ' Juan Daniel ',
            firstLastName: ' Forner ',
            secondLastNameNull: true
        );

        $this->assertEquals("Juan Daniel Forner", $user->getFullName());
    }

    public function testGetFullNameThrowsExceptionWhenNameIsNotSet()
    {
        $user = User::build()
            ->setFirstLastName('Doe')
            ->setSecondLastName('Smith');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot retrieve full name without setting name');
        $user->getFullName();
    }

    public function testGetFullNameThrowsExceptionWhenFirstLastNameIsNotSet()
    {
        $user = User::build()
            ->setName('Juan')
            ->setSecondLastName('Smith');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot retrieve full name without setting firstLastName');
        $user->getFullName();
    }

    public function testGetFullNameThrowsExceptionWhenSecondLastNameIsNotSet()
    {
        $user = User::build()
            ->setName('Juan')
            ->setFirstLastName('Doe');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot retrieve full name without setting secondLastName');
        $user->getFullName();
    }

    public function testGetFullNameThrowsExceptionWhenAllFieldsAreNotSet()
    {
        $user = User::build();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot retrieve full name without setting name, firstLastName, secondLastName');
        $user->getFullName();
    }

    public function testGetCreatedAt()
    {
        $user = $this->createTestUserEntity();
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testGetCreatedAtFails()
    {
        $user = User::build();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s createdAt is not set');
        $user->getCreatedAt();
    }

    public function testGetUpdatedAt()
    {
        $user = $this->createTestUserEntity();
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getUpdatedAt());
    }

    public function testGetUpdatedAtFails()
    {
        $user = User::build();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('User\'s updatedAt is not set');
        $user->getUpdatedAt();
    }

    public function testUpdateName()
    {
        $user = $this->createTestUserEntity();
        $created = $user->getUpdatedAt();

        $newName = 'Updated User';
        $user->updateName($newName);

        $updated = $user->getUpdatedAt();

        $this->assertEquals($newName, $user->getName());
        $this->assertNotEquals($created, $updated);
    }

    public function testUpdateNameFails()
    {
        $this->expectException(InvalidArgumentException::class);

        $user = $this->createTestUserEntity();
        $user->updateName('');
    }

    public function testUpdateFirstLastName()
    {
        $user = $this->createTestUserEntity();
        $created = $user->getUpdatedAt();

        $newName = 'Updated User';
        $user->updateFirstLastName($newName);

        $updated = $user->getUpdatedAt();

        $this->assertEquals($newName, $user->getFirstLastName());
        $this->assertNotEquals($created, $updated);
    }

    public function testUpdateFirstLastNameFails()
    {
        $this->expectException(InvalidArgumentException::class);

        $user = $this->createTestUserEntity();
        $user->updateFirstLastName('');
    }

    public function testUpdateSecondLastName()
    {
        $user = $this->createTestUserEntity();
        $created = $user->getUpdatedAt();

        $newName = 'Updated User';
        $user->updateSecondLastName($newName);
        $updated = $user->getUpdatedAt();

        $this->assertEquals($newName, $user->getSecondLastName());
        $this->assertNotEquals($created, $updated);
    }

    public function testUpdateSecondLastNameFails()
    {
        $this->expectException(InvalidArgumentException::class);

        $user = $this->createTestUserEntity();
        $user->updateSecondLastName('');
    }

    public function testUpdateEmail()
    {
        $user = $this->createTestUserEntity();
        $created = $user->getUpdatedAt();

        $newName = 'jdanielforga@gmail.com';
        $user->updateEmail($newName);
        $updated = $user->getUpdatedAt();

        $this->assertEquals($newName, $user->getEmail());
        $this->assertNotEquals($created, $updated);
    }

    public function testUpdateEmailFails()
    {
        $this->expectException(InvalidArgumentException::class);

        $user = $this->createTestUserEntity();
        $user->updateEmail('');
    }

    public function testUpdateCanExecCommand()
    {
        $user = $this->createTestUserEntity();
        $created = $user->getUpdatedAt();

        $user->updateCanExecCommands(true);
        $updated = $user->getUpdatedAt();

        $this->assertTrue($user->canExecCommands());
        $this->assertNotEquals($created, $updated);
    }

    public function testBulkUpdate()
    {
        $user = $this->createTestUserEntity();
        $created = $user->getUpdatedAt();

        $newName = 'Juan Daniel';
        $newFirstLastName = 'Forner';
        $newSecondLastName = 'Garriga';
        $newEmail = 'jdanielforga@gmail.com';

        $user->bulkUpdate(
            name: $newName,
            firstLastName: $newFirstLastName,
            secondLastName: $newSecondLastName,
            email: $newEmail,
            canExecCommands: true
        );
        $updated = $user->getUpdatedAt();

        $this->assertEquals($newName, $user->getName());
        $this->assertEquals($newFirstLastName, $user->getFirstLastName());
        $this->assertEquals($newSecondLastName, $user->getSecondLastName());
        $this->assertEquals($newEmail, $user->getEmail());
        $this->assertTrue($user->canExecCommands());
        $this->assertNotEquals($created, $updated);
    }

    public function testCreateUser()
    {
        $name = ' Juan Daniel ';
        $firstLastName = ' Forner ';
        $secondLastName = ' Garriga ';
        $email = 'jdanielforga@gmail.com';

        $user = User::create(
            name: $name,
            firstLastName: $firstLastName,
            secondLastName: $secondLastName,
            email: $email
        );

        $trimmedName = trim($name);
        $trimmedFirstLastName = trim($firstLastName);
        $trimmedSecondLastName = trim($secondLastName);

        $this->assertEquals($trimmedName, $user->getName());
        $this->assertEquals($trimmedFirstLastName, $user->getFirstLastName());
        $this->assertEquals($trimmedSecondLastName, $user->getSecondLastName());
        $this->assertEquals("$trimmedName $trimmedFirstLastName $trimmedSecondLastName", $user->getFullName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getUpdatedAt());
        $this->assertFalse($user->canExecCommands());
    }
}
