<?php


/**
 * This class adds structure of 'tibia_levelhistory' table to 'propel' DatabaseMap object.
 *
 *
 * This class was autogenerated by Propel 1.3.0-dev on:
 *
 * Sat Jun 20 23:47:36 2009
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class LevelHistoryMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.LevelHistoryMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap(LevelHistoryPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(LevelHistoryPeer::TABLE_NAME);
		$tMap->setPhpName('LevelHistory');
		$tMap->setClassname('LevelHistory');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, null);

		$tMap->addForeignPrimaryKey('CHARACTER_ID', 'CharacterId', 'INTEGER' , 'tibia_character', 'ID', true, null);

		$tMap->addColumn('LEVEL', 'Level', 'INTEGER', false, null);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null);

		$tMap->addForeignKey('REASON_ID', 'ReasonId', 'INTEGER', 'tibia_creature', 'ID', false, null);

	} // doBuild()

} // LevelHistoryMapBuilder