
<?php
require_once 'submit.php';
require_once 'usercredentials.php';
include("connect.php");

function saveOrderToCookie($orderData) {
    setcookie('pending_order', json_encode($orderData), time() + (86400), "/");
}

function getOrderFromCookie() {
    if (isset($_COOKIE['pending_order'])) {
        return json_decode($_COOKIE['pending_order'], true);
    }
    return null;
}

function clearOrderCookie() {
    setcookie('pending_order', "", time() - 3600, "/");
}

$selected = '';
$minQty = '';
$currQty = '';
$price = '';
$fav = '';

if (isset($_POST['selected'])) {
    if (isset($_SESSION["userid"])) {
        $selected = $_POST['selected'];
        $getprod = new Product();
        $result = $getprod->getUserProducts($selected);
    } else {
        $selected = $_POST['selected'];
        $getprod = new Product();
        $result = $getprod->getProducts($selected);
    }
    echo $result;
}

if(isset($_POST['currQty'])) {
    $currQty = $_POST['currQty'];
    $price = $_POST['price'];
    $total = new Product();
    $update = $total->getTotal($currQty, $price);
    echo $update;
}

if (isset($_POST['fav'])) {
    if (isset($_SESSION["username"])) {
        $fav = $_POST['fav'];
        $addfav = new Product();
        $res = $addfav->addFav($fav);
        echo $res;
    } else {
        echo "2";
    }

}

if (isset($_POST['removeFav'])) {
    if (isset($_SESSION["username"])) {
        $removeFav = $_POST['removeFav'];
        $remfav = new Product();
        $res = $remfav->remFav($removeFav);
        echo $res;
    } else {
        echo "2";
    }
}

if (isset($_POST['id'])) {
    if (isset($_SESSION['username'])) {
        $id = $_POST['id'];
        $bg = $_POST['bg'];
        $name = $_POST['name'];
        $qty = $_POST['qty'];
        $price = $_POST['price'];
        $total = $_POST['total'];
    
        // Save both to session and cookie
        $_SESSION["productID"] = $id;
        $_SESSION["productBG"] = $bg;
        $_SESSION["productName"] = $name;
        $_SESSION["productQty"] = $qty;
        $_SESSION["productPrice"] = $price;
        $_SESSION["productTotal"] = $total;

        // Create order data array
        $orderData = array(
            'productID' => $id,
            'productBG' => $bg,
            'productName' => $name,
            'productQty' => $qty,
            'productPrice' => $price,
            'productTotal' => $total
        );
        
        saveOrderToCookie($orderData);
        $confirm = "0";
    } else { 
        $confirm = "1"; 
    }
    echo $confirm;
}

if (isset($_POST['confirmCart'])) {
    if (isset($_SESSION["productID"])) {
        return 0;
    } else { return 1; }
}

if (isset($_POST['getter'])) {
    $getter = $_POST['getter'];
    
    if($getter == 1) {
        $orderData = null;
        
        // Check session first
        if(isset($_SESSION["productID"])) {
            $prodID = $_SESSION["productID"];
            $prodBG = $_SESSION["productBG"];
            $prodname = $_SESSION["productName"];
            $prodqty = $_SESSION["productQty"];
            $prodprice = $_SESSION["productPrice"];
            $prodtotal = $_SESSION["productTotal"];
        } 
        // If not in session, check cookie
        else {
            $orderData = getOrderFromCookie();
            if($orderData) {
                $prodID = $orderData['productID'];
                $prodBG = $orderData['productBG'];
                $prodname = $orderData['productName'];
                $prodqty = $orderData['productQty'];
                $prodprice = $orderData['productPrice'];
                $prodtotal = $orderData['productTotal'];
            }
        }

        if(isset($prodID) || $orderData) {
            $_SESSION["totalAmount"] = (intval($prodqty) * intval($prodprice));
            $_SESSION["totalAmount"] .= ".00";
            $prodtotal = $_SESSION["totalAmount"];

            $checkOrder = "<div class=\"order-holder\" data-target=\"checkID_1\" data-target-1=\"checkQty_1\" data-target-2=\"checkTotal_1\">";
            $checkOrder .= "<div id=\"checkID_1\" style=\"display: none;\">$prodID</div>";
            $checkOrder .= "<div class=\"ch-img\" style=\"height: 60px; width: 60px; background: url($prodBG) center no-repeat; background-size: cover; border-radius: 10px;\"></div>";
            $checkOrder .= "<div class=\"ch-info\">";
            $checkOrder .= "<p><strong>$prodname</strong></p>";
            $checkOrder .= "<p>Quantity : <strong id=\"checkQty_1\">$prodqty</strong></p>";
            $checkOrder .= "<p>Price: <strong>₱$prodprice</strong></p>";
            $checkOrder .= "<p>Subtotal: <strong>₱</strong><strong id=\"checkTotal_1\">$prodtotal</strong></p>";
            $checkOrder .= "</div>";
            $checkOrder .= "</div>";

            $_SESSION["productID"] = null;
        } else { 
            $checkOrder = "0";
        }
    } else { 
        $checkOrder = "0";
    }
    echo $checkOrder;
}

if (isset($_POST['cartId'])) {
    if (isset($_SESSION["username"])) {
        $id = $_POST['cartId'];
        $qty = $_POST['qty'];
        $price = $_POST['price'];
    
        $cart = new Product();
        $addcart = $cart->addToCart($id, $qty, $price);
    } else {
        $addcart = "0";
    }
    echo $addcart;
}

if (isset($_POST['openCart'])) {
    if (isset($_SESSION["username"])) {
        $_SESSION["goCart"] = 1;
        $alert = "1";
    } else { $alert = "0"; }
    echo $alert;
}

if (isset($_POST['getCart'])) {
    if (isset($_SESSION["goCart"])) {
        $alert = "0";
        $_SESSION["goCart"] = null;
    } else { $alert = "1"; }
    echo $alert;
}

///////////////

if (isset($_POST['user'])) {
    if (isset($_SESSION["userid"])) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['getUserInfo'])) {
    $mode = $_POST['mode'];
    $getUser = new User();
    $user = $getUser->displayUserData($mode);
    echo $user;
}

if (isset($_POST['editInfo'])) {
    $mode = $_POST['mode'];
    $modif = new User();
    $modifinfo = $modif->displayUserData($mode);
    echo $modifinfo;
}

if (isset($_POST['confirmChanges'])) {
    $fn = $_POST['fn'];
    $ln = $_POST['ln'];
    $mn = $_POST['mn'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $bday = $_POST['bday'];
    $contact = $_POST['contact'];
    $loc = $_POST['loc'];
    $blk = $_POST['blk'];
    $str = $_POST['street'];
    $brgy = $_POST['brgy'];
    $city = $_POST['city'];
    $prov = $_POST['prov'];
    $zip = $_POST['zip'];

    if (!isset($age) || $age === "") { $age = null; }
    if (!isset($contact) || $contact === "") { $contact = null; }
    if (!isset($loc) || $loc === "") { $loc = null; }
    if (!isset($blk) || $blk === "") { $blk = null; }
    if (!isset($mn) || $mn === "") { $mn = null; }
    if (!isset($str) || $str === "") { $str = null; }
    if (!isset($brgy) || $brgy === "") { $brgy = null; }
    if (!isset($city) || $city === "") { $city = null; }
    if (!isset($prov) || $prov === "") { $prov = null; }
    if (!isset($zip) || $zip === "") { $zip = null; }
    if (!isset($bday) || $bday === "") { $bday = null; }

    $confChanges = new User();
    $confirmChanges = $confChanges->insertData($fn, $ln, $mn, $age, $sex, $bday, $contact, $loc, $blk, $str, $brgy, $city, $prov, $zip);
    echo $confirmChanges;
}

if (isset($_POST['changeAge'])) {
    $bday = $_POST['bday'];
    $today = date("Y-m-d");
    $bdate = date_create($bday);
    $dateToday = date_create($today);
    $age = date_diff($bdate, $dateToday);
    echo $age->y;
}

if (isset($_POST['saveInfo'])) {
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $bday = $_POST['bday'];
    $contact = $_POST['contact'];

    if (!isset($age) || $age === "") { $age = null; }
    if (!isset($sex) || $sex === "") { $sex = null; }
    if (!isset($bday) || $bday === "") { $bday = null; }
    if (!isset($contact) || $contact === "") { $contact = null; }

    $save = new User();
    $saveInfo = $save->saveInfo($sex, $age, $bday, $contact);
}

if (isset($_POST['getFavs'])) {
    $getf = new User();
    $getfav = $getf->getFav();
    echo $getfav;
}

if (isset($_POST['remProd'])) {
    $favs = $_POST['pid'];
    $rems = new User();
    $remprod = $rems->removeprod($favs);
    echo $remprod;
}

if (isset($_POST['searchWord'])) {
    $word = $_POST['word'];
    $search = new Product();
    $searchWord = $search->searchWord($word);
    echo $searchWord;
}

if (isset($_POST['checkPass'])) {
    $pass = $_POST['pass'];
    $chPass = new User();
    $checkpass = $chPass->checkPass($pass);
    echo $checkpass;
}

if (isset($_POST['updateInfo'])) {
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $newpass = $_POST['newpass'];

    if (!isset($pass) || $pass == "" || !isset($newpass) || $newpass == "") {
        $pass = NULL;
        $newpass = NULL;
        $up = new User();
        $upInfo = $up->changeUsername($uname);
    } else {
        $uname = NULL;
        $up = new User();
        $upInfo = $up->changePass($uname, $newpass);
        if ($upInfo == "0" || $upInfo == '0' || $upInfo == 0) {
            session_unset();
            session_destroy();
        }
    }
    echo $upInfo;
}

if (isset($_POST['logOut'])) {
    session_destroy();
}

if (isset($_POST['getUname'])) {
    $getU = new User();
    $getUname = $getU->getUsername();
    echo $getUname;
}

if (isset($_POST['delAcc'])) {
    $del = new User();
    $delAcc = $del->deleteAcc();
    if (intval($delAcc) == 0) {
        session_destroy();
    }
    echo $delAcc;
}

if (isset($_POST['cart'])) {
    $_SESSION["count"] = null;
    $_SESSION["totalAmount"] = 0;
    $basket = new User();
    $getCart = $basket->getCart();
    echo $getCart;
}

if (isset($_POST['rem'])) {
    $remItem = new User();
    $rID = $_POST['rID'];
    $remItem->remCart($rID);  // Just call the function without assignment
}


if (isset($_POST['delItem'])) {
    $dID = $_POST['dID'];
    $del = new User();
    $del->remCart($dID);  // Just call the function without assignment
}

if (isset($_POST['passItem'])) {
    $pID = $_POST['pID'];
    $img = $_POST['image'];
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $total = $_POST['total'];
    $token = $_POST['count'];

    $_SESSION["totalAmount"] = $_SESSION["totalAmount"] + (intval($qty) * intval($price));
    $_SESSION["totalAmount"] .= ".00";

    $checkOrder = "<div class=\"order-holder\" data-target=\"checkID_$token\" data-target-1=\"checkQty_$token\" data-target-2=\"checkTotal_$token\">";
    $checkOrder .= "<div id=\"checkID_$token\" style=\"display: none;\">$pID</div>";
    $checkOrder .= "<div class=\"ch-img\" style=\"height: 60px; width: 60px; background: url($img) center no-repeat; background-size: cover; border-radius: 10px;\"></div>";
    $checkOrder .= "<div class=\"ch-info\">";
    $checkOrder .= "<p><strong>$name</strong></p>";
    $checkOrder .= "<p>Quantity : <strong id=\"checkQty_$token\">$qty</strong></p>";
    $checkOrder .= "<p>Price: <strong>₱$price</strong></p>";
    $checkOrder .= "<p>Subtotal: <strong>₱</strong><strong id=\"checkTotal_$token\">$total</strong></p>";
    $checkOrder .= "</div>";
    $checkOrder .= "</div>";
    echo $checkOrder;
}

if (isset($_POST['showInfo'])) {
    $show = new User();
    $showinfo = $show->showInfo();
    echo $showinfo;
}

if (isset($_POST['gettotal'])) {
    echo $_SESSION["totalAmount"];
}

if (isset($_POST['passOrder'])) {
    $user = new User();
    $userInfo = $user->showInfo();
    $userArray = json_decode($userInfo, true);
    
    if (empty($userArray['contact']) || empty($userArray['address'])) {
        echo "incomplete_info";
        return;
    }
    
    $cid = $_POST['cID'];
    $cqty = $_POST['cQty'];
    $ctotal = $_POST['cTotal'];

    $order = new User();
    $order->passOrder($cid, $cqty, $ctotal);  // Just call the function without assignment
    clearOrderCookie();  // Always clear cookie after order is passed
}

if (isset($_POST['showOrder'])) {
    $status = new User();
    $showorder = $status->orderDetails();
    echo $showorder;
}

if (isset($_POST['endTag'])) {
    $_SESSION["count"] = 0;
}

if (isset($_POST['showtransac'])) {
    $trans = new User();
    $getTransac = $trans->showTransac();
    echo $getTransac;
}

/////////////////// ADMIN PAGE /////////////////////

if (isset($_POST['refreshPage'])) {
    $sql = "SELECT * FROM userinfo WHERE dateRemoved IS NULL;";
    $users = "";
    $result = $connect->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()) {
            $users .= "
            <tr>
                <td>$row[UIID]</td>
                <td>$row[lname]</td>
                <td>$row[fname]</td>
                <td>$row[contact]</td>
                <td>$row[email]</td>
            </tr>
            ";
        }
    }
    echo $users;
}
if (isset($_POST['refreshOrder'])) {
    $c = 0;
    $sql = "SELECT orderinfo.*, userinfo.*, prodinfo.* FROM orderinfo INNER JOIN userinfo ON orderinfo.UIID = userinfo.UIID INNER JOIN prodinfo ON orderinfo.PIID = prodinfo.PIID WHERE orderinfo.dateDelivered IS NULL AND orderinfo.status != 'Delivered';";
    $orders = "";
    $orderlist = "";
    $oTag = "";
    $stats = "";
    $result = $connect->query($sql);
    if($result) {
        while($row = $result->fetch_assoc()) {
            if ($row["ordertag"] !== $oTag) {
                $oTag = $row["ordertag"];
                $orders = "
                            <div class=\"order-summary\" id=\"orderSummary_$c\">
                                <div class=\"summary\">
                                    <p id=\"orderTag_$c\">$row[ordertag]</p>
                                    <p>$row[UIID]</p>
                ";
                $countSql = "SELECT COUNT(TRID) as count FROM orderinfo WHERE ordertag = '$row[ordertag]';";
                $countresult = $connect->query($countSql);
                if ($countresult) {
                    while($countRow = $countresult->fetch_assoc()) {
                        $orders .= "<p>$countRow[count]</p>";
                    }
                }
                $orders .= "        <p>$row[total]</p>
                                    <p id=\"status_$c\">$row[status]</p>
                                    <div class=\"action-btn\">
                ";
                
                if ($row["status"] == "Verifying Order") { $stats = "Preparing"; }
                elseif ($row["status"] == "Preparing Order") { $stats = "Delivery"; }
                elseif ($row["status"] == "On Delivery") { $stats = "Delivered"; }

                $orders .= "            <button class = 'btn btn-primary btn-sm' data-target=\"orderTag_$c\" data-target-1=\"status_$c\" onclick=\"proceed(this)\">$stats</button>
                                        <button class = 'btn btn-danger btn-sm' data-target=\"orderTag_$c\" onclick=\"deleteOrder(this)\">Cancel</button>
                                        <button class=\"resize-btn\" data-target=\"orderSummary_$c\" data-target-1=\"drop_$c\" onclick=\"maximize(this)\">
                                            <i class=\"fa-solid fa-chevron-down\"></i>
                                            <i class=\"fa-solid fa-chevron-up\"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class=\"per-item\" id=\"drop_$c\">
                                    <table class=\"table table-hover\">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Type</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                ";

                $orderSql = "SELECT prodinfo.*, orderinfo.* FROM orderinfo INNER JOIN prodinfo ON orderinfo.PIID = prodinfo.PIID WHERE orderinfo.ordertag = '$row[ordertag]';";
                $orderResult = $connect->query($orderSql);
                if ($orderResult) {
                    while ($orderRow = $orderResult->fetch_assoc()) {
                        $orders .= "                <tr>
                                                        <td>$orderRow[name]</td>
                                                        <td>$orderRow[type]</td>
                                                        <td>$orderRow[price]</td>
                                                        <td>$orderRow[qty]</td>
                                                        <td>$orderRow[subtotal]</td>
                                                    </tr>
                                    ";
                    }
                }

                $orders .= "            </tbody>
                                    </table>
                                </div>
                            </div>
                ";
                $c++;
                $orderlist .= $orders;
            }
        }
    }
    echo $orderlist;
}

if (isset($_POST['proceed'])) {
    $tag = $_POST['tag'];
    $st = $_POST['stats'];
    $stats = "";
    $date = date("Y-m-d H:i");

    if ($st == "Verifying Order") { 
        $stats = "Preparing Order"; 
        $sql = "UPDATE orderinfo SET status = '$stats' WHERE ordertag = '$tag';";
        $connect->query($sql);
    }
    elseif ($st == "Preparing Order") { 
        $stats = "On Delivery"; 
        $sql = "UPDATE orderinfo SET status = '$stats' WHERE ordertag = '$tag';";
        $connect->query($sql);
    }
    elseif ($st == "On Delivery") { 
        $stats = "Delivered"; 
        $sql = "UPDATE orderinfo SET status = '$stats', dateDelivered = '$date' WHERE ordertag = '$tag';";
        $connect->query($sql);
    }    
}

if (isset($_POST['deleteOrder'])) {
    $tag = $_POST['tag'];
    $userid = $_SESSION["userid"];
    $sql = "DELETE FROM orderinfo WHERE orderinfo = '$tag';";
    $connect->query($sql);
}

if (isset($_POST['checkUserInfo'])) {
    $user = new User();
    $userInfo = $user->showInfo();
    $userArray = json_decode($userInfo, true);
    
    if (empty($userArray['contact']) || empty($userArray['address'])) {
        echo "incomplete";
    } else {
        echo "complete";
    }
}
?> 