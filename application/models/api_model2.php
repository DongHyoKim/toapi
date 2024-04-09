<?php
class Api_model2 extends CI_Model {

    public function __construct(){
        parent::__construct();        
    }

    // insertDB
	//function insertDB($order, $products, $options, $payments, $cards, $coupons, $benefits) {
	function insertDB($order, $products, $options, $payments, $cards, $coupons, $benefits) {

		global $db;

        // sql쿼리문을 만들자!
		// 기본쿼리문 배열
		$sp_arr = array(
			'order'     => "[VENDINGM].[dbo].[SP_ITMS_ORDER];1 ",
			'products'  => "[VENDINGM].[dbo].[SP_ITMS_ORDERPRODUCT];1 ",
			'options'   => "[VENDINGM].[dbo].[SP_ITMS_ORDERPRODUCTOPTION];1 ",
			'payments'  => "[VENDINGM].[dbo].[SP_ITMS_PAYMENTS];1 ",
			'cards'     => "[VENDINGM].[dbo].[SP_ITMS_CARDPAYMENTSDETAIL];1 ",
			'coupons'   => "[VENDINGM].[dbo].[SP_ITMS_COUPONPAYMENTSDETAIL];1 ",
			'benefits'  => "[VENDINGM].[dbo].[SP_ITMS_PROFITSALETOTAL];1 ",
		);
		// '?'를 배열의 키갯수 만큼 붙이자!!
		// order
		$questionmark = '';
		for ($i = 0;$i < count(array_keys($order))-1;$i++) { 
			$questionmark .= '?, '; 
		}
		$questionmark .= '? '; 
        $sp_order = $sp_arr['order'].$questionmark;
        
		// products
		$questionmark = '';
		for ($i = 0;$i < count(array_keys($products['0']))-1;$i++) { 
			$questionmark .= '?, '; 
		}
		$questionmark .= '? '; 
        $sp_products = $sp_arr['products'].$questionmark;

		if(is_array($options)) {
		    for ($j = 0;$j < count($options);$j++) {
                if(empty($options[$j])) $j++;
			    for ($k = 0;$k <count($options[$j]);$k++) {
                    if(empty($options[$j][$k])) $k++;
				    $questionmark = '';
	                for ($i = 0;$i < count(array_keys($options[$j][$k]))-1;$i++) { 
	                    $questionmark .= '?, ';
		            }
		            $questionmark .= '? ';   // 34
                    $sp_options = $sp_arr['options'].$questionmark;
		        }
		    }
		}
        // payments
		$questionmark = '';
		for ($i = 0;$i < count(array_keys($payments['0']))-1;$i++) { 
			$questionmark .= '?, '; 
		}
		$questionmark .= '? '; 
        $sp_payments = $sp_arr['payments'].$questionmark;
		// cards
		if(!empty($cards)) {
		    for ($j = 0;$j < count($cards);$j++) {
                if(empty($cards[$j])) $j++;
			    $questionmark = '';
		        for ($i = 0;$i < count(array_keys($cards[$j]))-1;$i++) { 
			        $questionmark .= '?, '; 
		        }
		        $questionmark .= '? '; 
                $sp_cards = $sp_arr['cards'].$questionmark;
			}
		}
        // coupons
		if(!empty($coupons)) {
		    for ($j = 0;$j < count($coupons);$j++) {
                if(empty($coupons[$j])) $j++;
    		    $questionmark = '';
		        for ($i = 0;$i < count(array_keys($coupons[$j]))-1;$i++) { 
			        $questionmark .= '?, '; 
		        }
		        $questionmark .= '? '; 
                $sp_coupons = $sp_arr['coupons'].$questionmark;
			}
		}
		// benefits
        if(!empty($benefits)) {
    		$questionmark = '';
		    for ($i = 0;$i < count(array_keys($benefits['0']))-1;$i++) { 
			    $questionmark .= '?, '; 
		    }
		    $questionmark .= '? '; 
            $sp_benefits = $sp_arr['benefits'].$questionmark;
        }

		// 자~~ 이제 들어갑니다. 시작~~
        //echo $sp_order;
		//print_r($order);
	    //exit;

		// transaction start
		$this->db->trans_start();
        // insert DataBase
		// order
        $this->db->query($sp_order,$order);
		// products
		for ($i = 0;$i < count($products);$i++) { 
			$this->db->query($sp_products,$products[$i]); 
		}
		// options
		if(is_array($options)) {
		    for ($i = 0;$i < count($options);$i++) {
                if(empty($options[$i])) $i++;
			    for ($j = 0;$j <count($options[$i]);$j++) {
                    if(empty($options[$i][$j])) $j++;
                    $this->db->query($sp_options,$options[$i][$j]);
		        }
		    }
		}
		// payments
		for ($i = 0;$i < count($payments);$i++) { 
			$this->db->query($sp_payments,$payments[$i]); 
		}
		// cards
        if(!empty($cards)) {
			for ($i = 0;$i < count($cards);$i++) {
                if(empty($cards[$i])) $i++;
			    $this->db->query($sp_cards,$cards[$i]);
			}
		}
		// coupons
        if(!empty($coupons)) {
			for ($i = 0;$i < count($coupons);$i++) {
                if(empty($coupons[$i])) $i++;			
			    $this->db->query($sp_coupons,$coupons[$i]);
			}
		}
		// benefits
        if(!empty($benefits)) {
			for ($i = 0;$i < count($benefits);$i++) {
			    $this->db->query($sp_benefits,$benefits[$i]);
			}
		}
        // transaction end
		$this->db->trans_complete();

		return $this->db->trans_status()? "0000" : -1;
    } 

}