<?php
/**
 * Asset
 *
 * PHP version 5
 *
 * @category Class
 * @package  XeroAPI\XeroPHP
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Xero Assets API
 *
 * The Assets API exposes fixed asset related functions of the Xero Accounting application and can be used for a variety of purposes such as creating assets, retrieving asset valuations etc.
 *
 * Contact: api@xero.com
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 4.3.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace XeroAPI\XeroPHP\Models\Asset;

use \ArrayAccess;
use \XeroAPI\XeroPHP\AssetObjectSerializer;
use \XeroAPI\XeroPHP\StringUtil;
/**
 * Asset Class Doc Comment
 *
 * @category Class
 * @package  XeroAPI\XeroPHP
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class Asset implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Asset';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'asset_id' => 'string',
        'asset_name' => 'string',
        'asset_type_id' => 'string',
        'asset_number' => 'string',
        'purchase_date' => '\DateTime',
        'purchase_price' => 'double',
        'disposal_date' => '\DateTime',
        'disposal_price' => 'double',
        'asset_status' => '\XeroAPI\XeroPHP\Models\Asset\AssetStatus',
        'warranty_expiry_date' => 'string',
        'serial_number' => 'string',
        'book_depreciation_setting' => '\XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting',
        'book_depreciation_detail' => '\XeroAPI\XeroPHP\Models\Asset\BookDepreciationDetail',
        'can_rollback' => 'bool',
        'accounting_book_value' => 'double',
        'is_delete_enabled_for_date' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPIFormats = [
        'asset_id' => 'uuid',
        'asset_name' => null,
        'asset_type_id' => 'uuid',
        'asset_number' => null,
        'purchase_date' => 'date',
        'purchase_price' => 'double',
        'disposal_date' => 'date',
        'disposal_price' => 'double',
        'asset_status' => null,
        'warranty_expiry_date' => null,
        'serial_number' => null,
        'book_depreciation_setting' => null,
        'book_depreciation_detail' => null,
        'can_rollback' => null,
        'accounting_book_value' => 'double',
        'is_delete_enabled_for_date' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'asset_id' => 'assetId',
        'asset_name' => 'assetName',
        'asset_type_id' => 'assetTypeId',
        'asset_number' => 'assetNumber',
        'purchase_date' => 'purchaseDate',
        'purchase_price' => 'purchasePrice',
        'disposal_date' => 'disposalDate',
        'disposal_price' => 'disposalPrice',
        'asset_status' => 'assetStatus',
        'warranty_expiry_date' => 'warrantyExpiryDate',
        'serial_number' => 'serialNumber',
        'book_depreciation_setting' => 'bookDepreciationSetting',
        'book_depreciation_detail' => 'bookDepreciationDetail',
        'can_rollback' => 'canRollback',
        'accounting_book_value' => 'accountingBookValue',
        'is_delete_enabled_for_date' => 'isDeleteEnabledForDate'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'asset_id' => 'setAssetId',
        'asset_name' => 'setAssetName',
        'asset_type_id' => 'setAssetTypeId',
        'asset_number' => 'setAssetNumber',
        'purchase_date' => 'setPurchaseDate',
        'purchase_price' => 'setPurchasePrice',
        'disposal_date' => 'setDisposalDate',
        'disposal_price' => 'setDisposalPrice',
        'asset_status' => 'setAssetStatus',
        'warranty_expiry_date' => 'setWarrantyExpiryDate',
        'serial_number' => 'setSerialNumber',
        'book_depreciation_setting' => 'setBookDepreciationSetting',
        'book_depreciation_detail' => 'setBookDepreciationDetail',
        'can_rollback' => 'setCanRollback',
        'accounting_book_value' => 'setAccountingBookValue',
        'is_delete_enabled_for_date' => 'setIsDeleteEnabledForDate'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'asset_id' => 'getAssetId',
        'asset_name' => 'getAssetName',
        'asset_type_id' => 'getAssetTypeId',
        'asset_number' => 'getAssetNumber',
        'purchase_date' => 'getPurchaseDate',
        'purchase_price' => 'getPurchasePrice',
        'disposal_date' => 'getDisposalDate',
        'disposal_price' => 'getDisposalPrice',
        'asset_status' => 'getAssetStatus',
        'warranty_expiry_date' => 'getWarrantyExpiryDate',
        'serial_number' => 'getSerialNumber',
        'book_depreciation_setting' => 'getBookDepreciationSetting',
        'book_depreciation_detail' => 'getBookDepreciationDetail',
        'can_rollback' => 'getCanRollback',
        'accounting_book_value' => 'getAccountingBookValue',
        'is_delete_enabled_for_date' => 'getIsDeleteEnabledForDate'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['asset_id'] = isset($data['asset_id']) ? $data['asset_id'] : null;
        $this->container['asset_name'] = isset($data['asset_name']) ? $data['asset_name'] : null;
        $this->container['asset_type_id'] = isset($data['asset_type_id']) ? $data['asset_type_id'] : null;
        $this->container['asset_number'] = isset($data['asset_number']) ? $data['asset_number'] : null;
        $this->container['purchase_date'] = isset($data['purchase_date']) ? $data['purchase_date'] : null;
        $this->container['purchase_price'] = isset($data['purchase_price']) ? $data['purchase_price'] : null;
        $this->container['disposal_date'] = isset($data['disposal_date']) ? $data['disposal_date'] : null;
        $this->container['disposal_price'] = isset($data['disposal_price']) ? $data['disposal_price'] : null;
        $this->container['asset_status'] = isset($data['asset_status']) ? $data['asset_status'] : null;
        $this->container['warranty_expiry_date'] = isset($data['warranty_expiry_date']) ? $data['warranty_expiry_date'] : null;
        $this->container['serial_number'] = isset($data['serial_number']) ? $data['serial_number'] : null;
        $this->container['book_depreciation_setting'] = isset($data['book_depreciation_setting']) ? $data['book_depreciation_setting'] : null;
        $this->container['book_depreciation_detail'] = isset($data['book_depreciation_detail']) ? $data['book_depreciation_detail'] : null;
        $this->container['can_rollback'] = isset($data['can_rollback']) ? $data['can_rollback'] : null;
        $this->container['accounting_book_value'] = isset($data['accounting_book_value']) ? $data['accounting_book_value'] : null;
        $this->container['is_delete_enabled_for_date'] = isset($data['is_delete_enabled_for_date']) ? $data['is_delete_enabled_for_date'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['asset_name'] === null) {
            $invalidProperties[] = "'asset_name' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets asset_id
     *
     * @return string|null
     */
    public function getAssetId()
    {
        return $this->container['asset_id'];
    }

    /**
     * Sets asset_id
     *
     * @param string|null $asset_id The Xero-generated Id for the asset
     *
     * @return $this
     */
    public function setAssetId($asset_id)
    {

        $this->container['asset_id'] = $asset_id;

        return $this;
    }



    /**
     * Gets asset_name
     *
     * @return string
     */
    public function getAssetName()
    {
        return $this->container['asset_name'];
    }

    /**
     * Sets asset_name
     *
     * @param string $asset_name The name of the asset
     *
     * @return $this
     */
    public function setAssetName($asset_name)
    {

        $this->container['asset_name'] = $asset_name;

        return $this;
    }



    /**
     * Gets asset_type_id
     *
     * @return string|null
     */
    public function getAssetTypeId()
    {
        return $this->container['asset_type_id'];
    }

    /**
     * Sets asset_type_id
     *
     * @param string|null $asset_type_id The Xero-generated Id for the asset type
     *
     * @return $this
     */
    public function setAssetTypeId($asset_type_id)
    {

        $this->container['asset_type_id'] = $asset_type_id;

        return $this;
    }



    /**
     * Gets asset_number
     *
     * @return string|null
     */
    public function getAssetNumber()
    {
        return $this->container['asset_number'];
    }

    /**
     * Sets asset_number
     *
     * @param string|null $asset_number Must be unique.
     *
     * @return $this
     */
    public function setAssetNumber($asset_number)
    {

        $this->container['asset_number'] = $asset_number;

        return $this;
    }



    /**
     * Gets purchase_date
     *
     * @return \DateTime|null
     */
    public function getPurchaseDate()
    {
        return $this->container['purchase_date'];
    }

    /**
     * Sets purchase_date
     *
     * @param \DateTime|null $purchase_date The date the asset was purchased YYYY-MM-DD
     *
     * @return $this
     */
    public function setPurchaseDate($purchase_date)
    {

        $this->container['purchase_date'] = $purchase_date;

        return $this;
    }



    /**
     * Gets purchase_price
     *
     * @return double|null
     */
    public function getPurchasePrice()
    {
        return $this->container['purchase_price'];
    }

    /**
     * Sets purchase_price
     *
     * @param double|null $purchase_price The purchase price of the asset
     *
     * @return $this
     */
    public function setPurchasePrice($purchase_price)
    {

        $this->container['purchase_price'] = $purchase_price;

        return $this;
    }



    /**
     * Gets disposal_date
     *
     * @return \DateTime|null
     */
    public function getDisposalDate()
    {
        return $this->container['disposal_date'];
    }

    /**
     * Sets disposal_date
     *
     * @param \DateTime|null $disposal_date The date the asset was disposed
     *
     * @return $this
     */
    public function setDisposalDate($disposal_date)
    {

        $this->container['disposal_date'] = $disposal_date;

        return $this;
    }



    /**
     * Gets disposal_price
     *
     * @return double|null
     */
    public function getDisposalPrice()
    {
        return $this->container['disposal_price'];
    }

    /**
     * Sets disposal_price
     *
     * @param double|null $disposal_price The price the asset was disposed at
     *
     * @return $this
     */
    public function setDisposalPrice($disposal_price)
    {

        $this->container['disposal_price'] = $disposal_price;

        return $this;
    }



    /**
     * Gets asset_status
     *
     * @return \XeroAPI\XeroPHP\Models\Asset\AssetStatus|null
     */
    public function getAssetStatus()
    {
        return $this->container['asset_status'];
    }

    /**
     * Sets asset_status
     *
     * @param \XeroAPI\XeroPHP\Models\Asset\AssetStatus|null $asset_status asset_status
     *
     * @return $this
     */
    public function setAssetStatus($asset_status)
    {

        $this->container['asset_status'] = $asset_status;

        return $this;
    }



    /**
     * Gets warranty_expiry_date
     *
     * @return string|null
     */
    public function getWarrantyExpiryDate()
    {
        return $this->container['warranty_expiry_date'];
    }

    /**
     * Sets warranty_expiry_date
     *
     * @param string|null $warranty_expiry_date The date the asset’s warranty expires (if needed) YYYY-MM-DD
     *
     * @return $this
     */
    public function setWarrantyExpiryDate($warranty_expiry_date)
    {

        $this->container['warranty_expiry_date'] = $warranty_expiry_date;

        return $this;
    }



    /**
     * Gets serial_number
     *
     * @return string|null
     */
    public function getSerialNumber()
    {
        return $this->container['serial_number'];
    }

    /**
     * Sets serial_number
     *
     * @param string|null $serial_number The asset's serial number
     *
     * @return $this
     */
    public function setSerialNumber($serial_number)
    {

        $this->container['serial_number'] = $serial_number;

        return $this;
    }



    /**
     * Gets book_depreciation_setting
     *
     * @return \XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting|null
     */
    public function getBookDepreciationSetting()
    {
        return $this->container['book_depreciation_setting'];
    }

    /**
     * Sets book_depreciation_setting
     *
     * @param \XeroAPI\XeroPHP\Models\Asset\BookDepreciationSetting|null $book_depreciation_setting book_depreciation_setting
     *
     * @return $this
     */
    public function setBookDepreciationSetting($book_depreciation_setting)
    {

        $this->container['book_depreciation_setting'] = $book_depreciation_setting;

        return $this;
    }



    /**
     * Gets book_depreciation_detail
     *
     * @return \XeroAPI\XeroPHP\Models\Asset\BookDepreciationDetail|null
     */
    public function getBookDepreciationDetail()
    {
        return $this->container['book_depreciation_detail'];
    }

    /**
     * Sets book_depreciation_detail
     *
     * @param \XeroAPI\XeroPHP\Models\Asset\BookDepreciationDetail|null $book_depreciation_detail book_depreciation_detail
     *
     * @return $this
     */
    public function setBookDepreciationDetail($book_depreciation_detail)
    {

        $this->container['book_depreciation_detail'] = $book_depreciation_detail;

        return $this;
    }



    /**
     * Gets can_rollback
     *
     * @return bool|null
     */
    public function getCanRollback()
    {
        return $this->container['can_rollback'];
    }

    /**
     * Sets can_rollback
     *
     * @param bool|null $can_rollback Boolean to indicate whether depreciation can be rolled back for this asset individually. This is true if it doesn't have 'legacy' journal entries and if there is no lock period that would prevent this asset from rolling back.
     *
     * @return $this
     */
    public function setCanRollback($can_rollback)
    {

        $this->container['can_rollback'] = $can_rollback;

        return $this;
    }



    /**
     * Gets accounting_book_value
     *
     * @return double|null
     */
    public function getAccountingBookValue()
    {
        return $this->container['accounting_book_value'];
    }

    /**
     * Sets accounting_book_value
     *
     * @param double|null $accounting_book_value The accounting value of the asset
     *
     * @return $this
     */
    public function setAccountingBookValue($accounting_book_value)
    {

        $this->container['accounting_book_value'] = $accounting_book_value;

        return $this;
    }



    /**
     * Gets is_delete_enabled_for_date
     *
     * @return bool|null
     */
    public function getIsDeleteEnabledForDate()
    {
        return $this->container['is_delete_enabled_for_date'];
    }

    /**
     * Sets is_delete_enabled_for_date
     *
     * @param bool|null $is_delete_enabled_for_date Boolean to indicate whether delete is enabled
     *
     * @return $this
     */
    public function setIsDeleteEnabledForDate($is_delete_enabled_for_date)
    {

        $this->container['is_delete_enabled_for_date'] = $is_delete_enabled_for_date;

        return $this;
    }


    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            AssetObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }
}


