<?php

/**
 * Functional tests for the SDK
 *
 * PHP version 5
 *
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   Microsoft
 * @package    Tests\Functional\WindowsAzure\Services\Table
 * @author     Jason Cooke <jcooke@microsoft.com>
 * @copyright  2012 Microsoft Corporation
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link       http://pear.php.net/package/azure-sdk-for-php
 */

namespace Tests\Functional\WindowsAzure\Services\Table;

use WindowsAzure\Services\Core\Models\Logging;
use WindowsAzure\Services\Core\Models\Metrics;
use WindowsAzure\Services\Core\Models\RetentionPolicy;
use WindowsAzure\Services\Core\Models\ServiceProperties;
use WindowsAzure\Services\Table\Models\DeleteEntityOptions;
use WindowsAzure\Services\Table\Models\EdmType;
use WindowsAzure\Services\Table\Models\Entity;
use WindowsAzure\Services\Table\Models\Property;
use WindowsAzure\Services\Table\Models\Query;
use WindowsAzure\Services\Table\Models\QueryEntitiesOptions;
use WindowsAzure\Services\Table\Models\QueryTablesOptions;
use WindowsAzure\Services\Table\Models\TableServiceOptions;
use WindowsAzure\Services\Table\Models\Filters\BinaryFilter;
use WindowsAzure\Services\Table\Models\Filters\ConstantFilter;
use WindowsAzure\Services\Table\Models\Filters\Filter;
use WindowsAzure\Services\Table\Models\Filters\LiteralFilter;
use WindowsAzure\Services\Table\Models\Filters\RawStringFilter;
use WindowsAzure\Services\Table\Models\Filters\UnaryFilter;

class TableServiceFunctionalOptionsTest extends \PHPUnit_Framework_TestCase {
    // -------------------------------
    // -- Check the Options classes --
    // -------------------------------

    public function testCheckTableServiceOptions() {
        $options = new TableServiceOptions();
        $this->assertNotNull($options, 'Default TableServiceOptions');
    }

    public function testCheckRetentionPolicy() {
        $rp = new RetentionPolicy();
        $this->assertNull($rp->getDays(), 'Default RetentionPolicy->getDays should be null');
        $this->assertNull($rp->getEnabled(), 'Default RetentionPolicy->getEnabled should be null');
        $rp->setDays(10);
        $rp->setEnabled(true);
        $this->assertEquals(10, $rp->getDays(), 'Set RetentionPolicy->getDays should be 10');
        $this->assertTrue($rp->getEnabled(), 'Set RetentionPolicy->getEnabled should be true');
    }

    public function testCheckLogging() {
        $rp = new RetentionPolicy();

        $l = new Logging();
        $this->assertNull($l->getRetentionPolicy(), 'Default Logging->getRetentionPolicy should be null');
        $this->assertNull($l->getVersion(), 'Default Logging->getVersion should be null');
        $this->assertNull($l->getDelete(), 'Default Logging->getDelete should be null');
        $this->assertNull($l->getRead(), 'Default Logging->getRead should be false');
        $this->assertNull($l->getWrite(), 'Default Logging->getWrite should be false');
        $l->setRetentionPolicy($rp);
        $l->setVersion('2.0');
        $l->setDelete(true);
        $l->setRead(true);
        $l->setWrite(true);
        
        $this->assertEquals($rp, $l->getRetentionPolicy(), 'Set Logging->getRetentionPolicy');
        $this->assertEquals('2.0', $l->getVersion(), 'Set Logging->getVersion');
        $this->assertTrue($l->getDelete(), 'Set Logging->getDelete should be true');
        $this->assertTrue($l->getRead(), 'Set Logging->getRead should be true');
        $this->assertTrue($l->getWrite(), 'Set Logging->getWrite should be true');
    }

    public function testCheckMetrics() {
        $rp = new RetentionPolicy();

        $m = new Metrics();
        $this->assertNull($m->getRetentionPolicy(), 'Default Metrics->getRetentionPolicy should be null');
        $this->assertNull($m->getVersion(), 'Default Metrics->getVersion should be null');
        $this->assertNull($m->getEnabled(), 'Default Metrics->getEnabled should be false');
        $this->assertNull($m->getIncludeAPIs(), 'Default Metrics->getIncludeAPIs should be null');
        $m->setRetentionPolicy($rp);
        $m->setVersion('2.0');
        $m->setEnabled(true);
        $m->setIncludeAPIs(true);
        $this->assertEquals($rp, $m->getRetentionPolicy(), 'Set Metrics->getRetentionPolicy');
        $this->assertEquals('2.0', $m->getVersion(), 'Set Metrics->getVersion');
        $this->assertTrue($m->getEnabled(), 'Set Metrics->getEnabled should be true');
        $this->assertTrue($m->getIncludeAPIs(), 'Set Metrics->getIncludeAPIs should be true');
    }

    public function testCheckServiceProperties() {
        $l = new Logging();
        $m = new Metrics();

        $sp = new ServiceProperties();
        $this->assertNull($sp->getLogging(), 'Default ServiceProperties->getLogging should not be null');
        $this->assertNull($sp->getMetrics(), 'Default ServiceProperties->getMetrics should not be null');

        $sp->setLogging($l);
        $sp->setMetrics($m);
        $this->assertEquals($sp->getLogging(), $l, 'Set ServiceProperties->getLogging');
        $this->assertEquals($sp->getMetrics(), $m, 'Set ServiceProperties->getMetrics');
    }

    public function testCheckQueryTablesOptions() {
        $options = new QueryTablesOptions();
        $nextTableName = 'foo';
        $query = new Query();

        $this->assertNull($options->getNextTableName(), 'Default QueryTablesOptions->getNextTableName');
        $this->assertNull($options->getQuery(), 'Default QueryTablesOptions->getQuery');
        $options->setNextTableName($nextTableName);
        $options->setQuery($query);
        $this->assertEquals($nextTableName, $options->getNextTableName(), 'Set QueryTablesOptions->getNextTableName');
        $this->assertEquals($query, $options->getQuery(), 'Set QueryTablesOptions->getQuery');
    }

    public function testCheckDeleteEntityOptions() {
        $options = new DeleteEntityOptions();
        $etag = 'foo';

        $this->assertNull($options->getEtag(), 'Default DeleteEntityOptions->getEtag');
        $options->setEtag($etag);
        $this->assertEquals($etag, $options->getEtag(), 'Set DeleteEntityOptions->getEtag');
    }

    public function testCheckQueryEntitiesOptions() {
        $options = new QueryEntitiesOptions();
        $query = new Query();
        $nextPartitionKey = 'aaa';
        $nextRowKey = 'bbb';

        $this->assertNull($options->getNextPartitionKey(), 'Default QueryEntitiesOptions->getNextPartitionKey');
        $this->assertNull($options->getNextRowKey(), 'Default QueryEntitiesOptions->getNextRowKey');
        $this->assertNull($options->getQuery(), 'Default QueryEntitiesOptions->getQuery');
        $options->setNextPartitionKey($nextPartitionKey);
        $options->setNextRowKey($nextRowKey);
        $options->setQuery($query);
        $this->assertEquals($nextPartitionKey, $options->getNextPartitionKey(), 'Set QueryEntitiesOptions->getNextPartitionKey');
        $this->assertEquals($nextRowKey, $options->getNextRowKey(), 'Set QueryEntitiesOptions->getNextRowKey');
        $this->assertEquals($query, $options->getQuery(), 'Set QueryEntitiesOptions->getQuery');
    }

    public function testCheckQuery() {
        $query = new Query();
        $this->assertNull($query->getFilter(), 'Default Query->getFilter');
        $this->assertNull($query->getSelectFields(), 'Default Query->getSelectFields');
        $this->assertNull($query->getTop(), 'Default Query->getTop');

        $query->addSelectField('bar');
        $query->addSelectField('baz');
        $this->assertNotNull($query->getSelectFields(), 'Add Query->getSelectFields');
        $this->assertEquals(2, count($query->getSelectFields()), 'Add Query->getSelectFields->size');

        $filter = Filter::applyConstant('foo', EdmType::STRING);
        $query->setFilter($filter);
        $query->setSelectFields(null);
        $query->setTop(TableServiceFunctionalTestData::IntegerMAX_VALUE);

        $this->assertEquals($filter, $query->getFilter(), 'Set Query->getFilter');
        $this->assertNull($query->getSelectFields(), 'Set Query->getSelectFields');
        $this->assertEquals(TableServiceFunctionalTestData::IntegerMAX_VALUE, $query->getTop(), 'Set Query->getTop');
    }

    public function testCheckFilter() {
        $filter = new Filter();
        $this->assertNotNull($filter, 'Default $filter');
    }

    public function testCheckBinaryFilter() {
        $filter = new BinaryFilter();
        $this->assertNotNull($filter, 'Default $filter');

        $this->assertNull($filter->getLeft(), 'Default BinaryFilter->getFilter');
        $this->assertNull($filter->getOperator(), 'Default BinaryFilter->getOperator');
        $this->assertNull($filter->getRight(), 'Default BinaryFilter->getRight');

        $left = new UnaryFilter();
        $operator = 'foo';
        $right = new ConstantFilter();

        $filter->setLeft($left);
        $filter->setOperator($operator);
        $filter->setRight($right);

        $this->assertEquals($left, $filter->getLeft(), 'Set BinaryFilter->getLeft');
        $this->assertEquals($operator, $filter->getOperator(), 'Set BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'Set BinaryFilter->getRight');

        // Now check the factory.
        $filter = Filter::applyAnd($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'and factory BinaryFilter->getLeft');
        $this->assertEquals('and', $filter->getOperator(), 'and factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'and factory BinaryFilter->getRight');

        $filter = Filter::applyEq($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'eq factory BinaryFilter->getLeft');
        $this->assertEquals('eq', $filter->getOperator(), 'eq factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'eq factory BinaryFilter->getRight');

        $filter = Filter::applyGe($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'ge factory BinaryFilter->getLeft');
        $this->assertEquals('ge', $filter->getOperator(), 'ge factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'ge factory BinaryFilter->getRight');

        $filter = Filter::applyGt($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'gt factory BinaryFilter->getLeft');
        $this->assertEquals('gt', $filter->getOperator(), 'gt factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'gt factory BinaryFilter->getRight');

        $filter = Filter::applyLe($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'le factory BinaryFilter->getLeft');
        $this->assertEquals('le', $filter->getOperator(), 'le factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'le factory BinaryFilter->getRight');

        $filter = Filter::applyLt($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'lt factory BinaryFilter->getLeft');
        $this->assertEquals('lt', $filter->getOperator(), 'lt factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'lt factory BinaryFilter->getRight');

        $filter = Filter::applyNe($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'ne factory BinaryFilter->getLeft');
        $this->assertEquals('ne', $filter->getOperator(), 'ne factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'ne factory BinaryFilter->getRight');

        $filter = Filter::applyOr($left, $right);
        $this->assertEquals($left, $filter->getLeft(), 'or factory BinaryFilter->getLeft');
        $this->assertEquals('or', $filter->getOperator(), 'or factory BinaryFilter->getOperator');
        $this->assertEquals($right, $filter->getRight(), 'or factory BinaryFilter->getRight');
    }

    public function testCheckConstantFilter() {
        $filter = new ConstantFilter();
        $this->assertNotNull($filter, 'Default $filter');

        $this->assertNull($filter->getValue(), 'Default ConstantFilter->getValue');

        $value = 'foo';
        $filter->setValue($value);

        $this->assertEquals($value, $filter->getValue(), 'Set ConstantFilter->getValue');

        // Now check the factory.
        $value = 'bar';
        $filter = Filter::applyConstant($value, EdmType::STRING);
        $this->assertEquals($value, $filter->getValue(), 'constant factory ConstantFilter->getValue');
    }

    public function testCheckLiteralFilter() {
        $filter = new LiteralFilter();
        $this->assertNotNull($filter, 'Default $filter');

        $this->assertNull($filter->getLiteral(), 'Default LiteralFilter->getLiteral');

        $literal = 'foo';
        $filter->setLiteral($literal);
        $this->assertEquals($literal, $filter->getLiteral(), 'Set LiteralFilter->getLiteral');

        // Now check the factory.
        $literal = 'bar';
        $filter = Filter::applyliteral($literal);
        $this->assertEquals($literal, $filter->getLiteral(), 'Literal factory LiteralFilter->getLiteral');
    }

    public function testCheckRawStringFilter() {
        $filter = new RawStringFilter();
        $this->assertNotNull($filter, 'Default $filter');

        $this->assertNull($filter->getRawString(), 'Default RawStringFilter->getRawString');

        $rawString = 'foo';
        $filter->setRawString($rawString);
        $this->assertEquals($rawString, $filter->getRawString(), 'Set RawStringFilter->getRawString');

        // Now check the factory.
        $rawString = 'bar';
        $filter = Filter::applyRawString($rawString);
        $this->assertEquals($rawString, $filter->getRawString(), 'RawString factory RawStringFilter->getRawString');
    }

    public function testCheckUnaryFilter() {
        $filter = new UnaryFilter();
        $this->assertNotNull($filter, 'Default $filter');

        $this->assertNull($filter->getOperand(), 'Default UnaryFilter->getOperand');
        $this->assertNull($filter->getOperator(), 'Default UnaryFilter->getOperator');

        $operand = new BinaryFilter();
        $operator = 'foo';
        $filter->setOperand($operand);
        $filter->setOperator($operator);
        $this->assertEquals($operand, $filter->getOperand(), 'Set UnaryFilter->getOperand');
        $this->assertEquals($operator, $filter->getOperator(), 'Set UnaryFilter->getOperator');

        // Now check the factory.
        $operand = new ConstantFilter();
        $filter = Filter::applyNot($operand);
        $this->assertEquals($operand, $filter->getOperand(), 'Unary factory UnaryFilter->getOperand');
        $this->assertEquals('not', $filter->getOperator(), 'Unary factory UnaryFilter->getOperator');
    }

    public function testCheckProperty() {
        $property = new Property();
        $maxv = TableServiceFunctionalTestData::IntegerMAX_VALUE;
        $edmType = EdmType::STRING;
        $this->assertNotNull($property, 'Default Property');
        $this->assertNull($property->getValue(), 'Default Property->getValue');
        $this->assertNull($property->getEdmType(), 'Default Property->getEdmType');
        $property->setValue($maxv);
        $property->setEdmType($edmType);
        $this->assertEquals($maxv, $property->getValue(), 'Set Property->getValue');
        $this->assertEquals($edmType, $property->getEdmType(), 'Set Property->getEdmType');
    }

    public function testCheckEntity() {
        $entity = new Entity();
        $etag = 'custom $etag';
        $partitionKey = 'custom partiton key';
        $rowKey = 'custom rowkey';
        $dates = TableServiceFunctionalTestData::getInterestingGoodDates();
        $timestamp = $dates[1];

        $property = new Property();
        $property->setEdmType(EdmType::INT32);
        $property->setValue(1234);
        $name = 'my name';
        $edmType = EdmType::STRING;
        $value = 'my value';

        $properties = array();
        $properties['goo'] = new Property();
        $properties['moo'] = new Property();

        $this->assertNotNull($entity, 'Default Entity');
        $this->assertNull($entity->getProperties(), 'Default Entity->getProperties');
        $this->assertNull($entity->getEtag(), 'Default Entity->getEtag');
        $this->assertNull($entity->getPartitionKey(), 'Default Entity->getPartitionKey');
        $this->assertNull($entity->getRowKey(), 'Default Entity->getRowKey');
        $this->assertNull($entity->getTimestamp(), 'Default Entity->getTimestamp');
        // TODO: Fails because of https://github.com/WindowsAzure/azure-sdk-for-php/issues/156
        $this->assertNull($entity->getProperty('foo'), 'Default Entity->getProperty(\'foo\')');
        $this->assertNull($entity->getPropertyValue('foo'), 'Default Entity->tryGtPropertyValue(\'foo\')');

        // Now set some things.
        $entity->setEtag($etag);
        $entity->setPartitionKey($partitionKey);
        $entity->setRowKey($rowKey);
        $entity->setTimestamp($timestamp);

        $this->assertEquals($etag, $entity->getEtag(), 'Default Entity->getEtag');
        $this->assertEquals($partitionKey, $entity->getPartitionKey(), 'Default Entity->getPartitionKey');
        $this->assertEquals($rowKey, $entity->getRowKey(), 'Default Entity->getRowKey');
        $this->assertEquals($timestamp, $entity->getTimestamp(), 'Default Entity->getTimestamp');

        $entity->setProperty($name, $property);
        $this->assertEquals($property, $entity->getProperty($name), 'Default Entity->getProperty(\'' . $name . '\')');

        $entity->addProperty($name, $edmType, $value);
        $this->assertEquals($value, $entity->getPropertyValue($name), 'Default Entity->getPropertyValue(\'' . $name . '\')');
        $this->assertEquals($edmType, $entity->getProperty($name)->getEdmType(), 'Default Entity->getProperty(\'' . $name . '\')->getEdmType');
        $this->assertEquals($value, $entity->getProperty($name)->getValue(), 'Default Entity->getProperty(\'' . $name . '\')->getValue');
        $this->assertTrue($property != $entity->getProperty($name), 'Default Entity->getProperty(\'' . $name . '\') changed');

        $entity->setProperties($properties);
        $this->assertNotNull($entity->getProperties(), 'Default Entity->getProperties');
        $this->assertEquals($properties, $entity->getProperties(), 'Default Entity->getProperties');
    }
}

?>
