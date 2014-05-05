<?php

namespace mako\tests\integration\database\midgard;

use \DateTime;

// --------------------------------------------------------------------------
// START CLASSES
// --------------------------------------------------------------------------

class TestUser extends \TestORM
{
	protected $tableName = 'users';
}

class TestUserReadOnly extends TestUser
{
	protected $readOnly = true;
}

class OptimisticLock extends \TestORM
{
	protected $tableName = 'optimistic_locks';

	protected $enableLocking = true;
}

// --------------------------------------------------------------------------
// END CLASSES
// --------------------------------------------------------------------------

/**
 * @group integration
 * @group integration:database
 * @requires extension PDO
 * @requires extension pdo_sqlite
 */

class ORMTest extends \ORMTestCase
{
	/**
	 * 
	 */

	public function testGet()
	{
		$user = TestUser::get(1);

		$this->assertInstanceOf('mako\tests\integration\database\midgard\TestUser', $user);

		$this->assertEquals(1, $user->id);

		$this->assertEquals('2014-04-30 14:40:01', $user->created_at);

		$this->assertEquals('foo', $user->username);

		$this->assertEquals('foo@example.org', $user->email);
	}

	/**
	 * 
	 */

	public function testGetNonExistent()
	{
		$user = TestUser::get(999);

		$this->assertFalse($user);
	}

	/**
	 * 
	 */

	public function testAll()
	{
		$users = TestUser::all();

		$this->assertInstanceOf('\mako\database\midgard\ResultSet', $users);

		foreach($users as $user)
		{
			$this->assertInstanceOf('mako\tests\integration\database\midgard\TestUser', $user);
		}
	}

	/**
	 * 
	 */

	public function testReload()
	{
		$user = TestUser::get(1);

		$user->username = 'bax';

		$this->assertEquals('bax', $user->username);

		$reloaded = $user->reload();

		$this->assertTrue($reloaded);

		$this->assertEquals('foo', $user->username);
	}

	/**
	 * 
	 */

	public function testReloadNonExistent()
	{
		$user = new TestUser;

		$reloaded = $user->reload();

		$this->assertFalse($reloaded);
	}

	/**
	 * 
	 */

	public function testSave()
	{
		$dateTime = new DateTime;

		$user = new TestUser();

		$user->username = 'bax';

		$user->email = 'bax@example.org';

		$user->created_at = $dateTime;

		$user->save();

		$this->assertFalse(empty($user->id));

		$this->assertEquals('bax', $user->username);

		$this->assertEquals('bax@example.org', $user->email);

		$this->assertEquals($dateTime, $user->created_at);

		$user->delete();
	}

	/**
	 * 
	 */

	public function testCreate()
	{
		$dateTime = new DateTime;

		$user = TestUser::create(['username' => 'bax', 'email' => 'bax@example.org', 'created_at' => $dateTime]);

		$this->assertFalse(empty($user->id));

		$this->assertEquals('bax', $user->username);

		$this->assertEquals('bax@example.org', $user->email);

		$this->assertEquals($dateTime, $user->created_at);

		$user->delete();
	}

	/**
	 * 
	 */

	public function testUpdate()
	{
		$dateTime = new DateTime;

		$user = TestUser::create(['username' => 'bax', 'email' => 'bax@example.org', 'created_at' => $dateTime]);

		$id = $user->id;

		$user = TestUser::get($id);

		$user->username = 'foo';

		$user->save();

		$user = TestUser::get($id);

		$this->assertEquals('foo', $user->username);

		$user->delete();
	}

	/**
	 * 
	 */

	public function testDelete()
	{
		$dateTime = new DateTime;

		$user = TestUser::create(['username' => 'bax', 'email' => 'bax@example.org', 'created_at' => $dateTime]);

		$count = TestUser::count();

		$user->delete();

		$this->assertEquals(($count - 1), TestUser::count());
	}

	/**
	 * @expectedException \mako\database\midgard\ReadOnlyRecordException
	 */

	public function saveReadOnly()
	{
		$dateTime = new DateTime;

		$user = new TestUserReadOnly();

		$user->username = 'bax';

		$user->email = 'bax@example.org';

		$user->created_at = $dateTime;

		$user->save();
	}

	/**
	 * @expectedException \mako\database\midgard\ReadOnlyRecordException
	 */

	public function testCreateReadOnly()
	{
		$dateTime = new DateTime;

		$user = TestUserReadOnly::create(['username' => 'bax', 'email' => 'bax@example.org', 'created_at' => $dateTime]);
	}

	/**
	 * @expectedException \mako\database\midgard\ReadOnlyRecordException
	 */

	public function testUpdateReadOnly()
	{
		$user = TestUserReadOnly::get(1);

		$user->username = 'bax';

		$user->save();
	}

	/**
	 * @expectedException \mako\database\midgard\ReadOnlyRecordException
	 */

	public function testDeleteReadOnly()
	{
		$user = TestUserReadOnly::get(1);

		$user->delete();
	}

	/**
	 * @expectedException \mako\database\midgard\StaleRecordException
	 */

	public function testOptimisticLockUpdate()
	{
		$record1 = OptimisticLock::ascending('id')->limit(1)->first();

		$record2 = OptimisticLock::ascending('id')->limit(1)->first();

		$record1->value = 'bar';

		$record1->save();

		$record2->value = 'bar';

		$record2->save();
	}

	/**
	 * @expectedException \mako\database\midgard\StaleRecordException
	 */

	public function testOptimisticLockDelete()
	{
		$record1 = OptimisticLock::ascending('id')->limit(1)->first();

		$record2 = OptimisticLock::ascending('id')->limit(1)->first();

		$record1->value = 'bar';

		$record1->save();

		$record2->delete();
	}

	/**
	 * 
	 */

	public function testClone()
	{
		$user = TestUser::get(1);

		$clone = clone $user;

		$this->assertTrue(empty($clone->id));

		$this->assertEquals($clone->created_at, $user->created_at);

		$this->assertEquals($clone->username, $user->username);

		$this->assertEquals($clone->email, $user->email);

		$clone->save();

		$this->assertFalse(empty($clone->id));

		$clone->delete();
	}

	/**
	 * 
	 */

	public function testToArray()
	{
		$user = TestUser::get(1)->toArray();

		$this->assertEquals(['id' => '1', 'created_at' => '2014-04-30 14:40:01', 'username' => 'foo', 'email' => 'foo@example.org'], $user);
	}

	/**
	 * 
	 */

	public function testToJSON()
	{
		$user = TestUser::get(1)->toJSON();

		$this->assertEquals('{"id":"1","created_at":"2014-04-30 14:40:01","username":"foo","email":"foo@example.org"}', $user);
	}

	/**
	 * 
	 */

	public function testHydratorForwarding()
	{
		$user = TestUser::where('id', '=', 1)->first();

		$this->assertInstanceOf('mako\tests\integration\database\midgard\TestUser', $user);
	}
}