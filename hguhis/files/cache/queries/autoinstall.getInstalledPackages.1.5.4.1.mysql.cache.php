<?php if(!defined('__ZBXE__')) exit();
$query = new Query();
$query->setQueryId("getInstalledPackages");
$query->setAction("select");
$query->setPriority("");

${'package_list26_argument'} = new ConditionArgument('package_list', $args->package_list, 'in');
${'package_list26_argument'}->checkNotNull();
${'package_list26_argument'}->createConditionValue();
if(!${'package_list26_argument'}->isValid()) return ${'package_list26_argument'}->getErrorMessage();
if(${'package_list26_argument'} !== null) ${'package_list26_argument'}->setColumnType('number');

$query->setColumns(array(
new SelectExpression('`installed`.*')
,new SelectExpression('`path`')
));
$query->setTables(array(
new Table('`xe_ai_installed_packages`', '`installed`')
,new Table('`xe_autoinstall_packages`', '`package`')
));
$query->setConditions(array(
new ConditionGroup(array(
new ConditionWithArgument('`installed`.`package_srl`',$package_list26_argument,"in")
,new ConditionWithoutArgument('`installed`.`package_srl`','`package`.`package_srl`',"equal", 'and')))
));
$query->setGroups(array());
$query->setOrder(array());
$query->setLimit();
return $query; ?>