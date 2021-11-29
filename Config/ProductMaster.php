<?php 

require_once __DIR__ . '/Database.php';

class ProductMaster extends Database
{

    private const PRODUCT_MASTER_TABLE = 'sap_product_masters';
	
	public $ProductCode;
	public $ProName;
	public $ProShortName;
	public $ProBrandCode;
	public $ProCategoryCode;
	public $Minimum;
	public $Maximum;
	public $ProStatus;
	public $VatStatus;
	public $VatNo;
	public $SalesOrganization;
	public $DistributionChannel;
	public $MaterialType;
	public $BaseUnit;
	public $NetWeight;
	public $WeightUnit;
	public $NetVolume;
	public $VolumeUnit;
	public $MaterialGroup1;
	public $MaterialGroup2;
	public $MaterialGroup3;
	public $MaterialGroup4;
	public $MaterialGroup5;


    public function __construct($product_list = null)
    {
		parent::__construct();
        $this->ProductCode = ($product_list) ? $product_list['ProductCode'] : null;
        $this->ProName = ($product_list) ? $product_list['ProName'] : null;
        $this->ProShortName = ($product_list) ? $product_list['ProShortName'] : null;
        $this->ProBrandCode = ($product_list) ? $product_list['ProBrandCode'] : null;
        $this->ProCategoryCode = ($product_list) ? $product_list['ProCategoryCode'] : null;
        $this->Minimum = ($product_list) ? $product_list['Minimum'] : null;
        $this->Maximum = ($product_list) ? $product_list['Maximum'] : null;
        $this->ProStatus = ($product_list) ? $product_list['ProStatus'] : null;
        $this->VatStatus = ($product_list) ? $product_list['VatStatus'] : null;
        $this->VatNo = ($product_list) ? $product_list['VatNo'] : null;
        $this->SalesOrganization = ($product_list) ? $product_list['SalesOrganization'] : null;
        $this->DistributionChannel = ($product_list) ? $product_list['DistributionChannel'] : null;
        $this->MaterialType = ($product_list) ? $product_list['MaterialType'] : null;
        $this->BaseUnit = ($product_list) ? $product_list['BaseUnit'] : null;
        $this->NetWeight = ($product_list) ? $product_list['NetWeight'] : null;
        $this->WeightUnit = ($product_list) ? $product_list['WeightUnit'] : null;
        $this->NetVolume = ($product_list) ? $product_list['NetVolume'] : null;
        $this->VolumeUnit = ($product_list) ? $product_list['VolumeUnit'] : null;
        $this->MaterialGroup1 = ($product_list) ? $product_list['MaterialGroup1'] : null;
        $this->MaterialGroup2 = ($product_list) ? $product_list['MaterialGroup2'] : null;
        $this->MaterialGroup3 = ($product_list) ? $product_list['MaterialGroup3'] : null;
        $this->MaterialGroup4 = ($product_list) ? $product_list['MaterialGroup4'] : null;
        $this->MaterialGroup5 = ($product_list) ? $product_list['MaterialGroup5'] : null;
    }

	public function insert_product_master($product_master) {
        $table = self::PRODUCT_MASTER_TABLE;
		$all_query = true;
        $this->connection->autocommit(FALSE);

		$columns = implode(", ",array_keys($product_master));
		$values  = "'" . implode( "','", array_values($product_master) ) . "'";
		$sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->connection->query($sql) ? null : $all_query = false;
        if($all_query) {
            $this->connection->commit();
            return true;
        }
        else {
            $this->connection->rollback();
            return false;
        }
	}

    public function check_duplicate_product($product_code) {
        $table = self::PRODUCT_MASTER_TABLE;
        $sql = "SELECT * FROM $table WHERE ProductCode = '$product_code'";
        $query = $this->connection->query($sql);
        if($query->num_rows > 0){
            return true;
        }
        else{
            return false;
        } 
    }
}