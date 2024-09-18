<form action="" method="get">
    <input type="submit" value="Install" name="install">
</form>
<?php

$db = new mysqli("localhost","root","mysql");
$db->query("CREATE DATABASE IF NOT EXISTS emir_senel");
$db = new mysqli("localhost","root","mysql","emir_senel");


if(isset($_GET['install'])){
    CreateTables();
    InsertData();
}
function CreateTables()
{   
    global $db;
    $db->query("
    CREATE TABLE IF NOT EXISTS DISTRICT(
        id INT,
        name VARCHAR(50)
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS CITY(
        id INT,
        districtid INT,
        name VARCHAR(50)
    );
	");
	$db->query("
    CREATE TABLE IF NOT EXISTS HOTEL(
		id INT,
        name VARCHAR(50)
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS HOTEL_LOCATIONS(
		id INT,
        cityid INT,
        name VARCHAR(50)
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS CLIENT(
        id INT,
        name VARCHAR(50),
        surname VARCHAR(50)
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS FACILITIES(
        id INT,
        hotelid INT,
        name VARCHAR(50)
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS TRAVELAGENT(
        id INT,
        name VARCHAR(50),
        type VARCHAR(50)
    );
    ");
    
    $db->query("
    CREATE TABLE IF NOT EXISTS ROOMTYPE(
        id INT,
        type VARCHAR(50),
        price FLOAT
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS ROOM(
        id INT,
        hotelid INT,
        typeid INT,
        floor INT
    );
    ");
    $db->query("
    CREATE TABLE IF NOT EXISTS BOOKING(
        id INT,
        booking_date DATE,
        checkin_date DATE,
        checkout_date DATE,
        agencyid INT,
        clientid INT,
        hotelid INT,
        roomid INT
    );
    ");
}
function GetRandomNameSurname($nameArr)
{
    $idx = rand(0,sizeof($nameArr)-1);
    $idx2 = rand(0,sizeof($nameArr)-1);
    if(strlen($nameArr[$idx])>1 && strlen($nameArr[$idx2])>1)
    return [explode(";",$nameArr[$idx])[0],explode(";",$nameArr[$idx2])[1]];
    else return GetRandomNameSurname($nameArr);
}
function InsertData()
{
    global $db;
    $db->query("DELETE FROM DISTRICT");
    $db->query("DELETE FROM CITY");
    $db->query("DELETE FROM HOTEL");
    $db->query("DELETE FROM CLIENT");
    $db->query("DELETE FROM FACILITIES");
    $db->query("DELETE FROM TRAVELAGENT");
    $db->query("DELETE FROM ROOMTYPE");
    $db->query("DELETE FROM ROOM");
    $db->query("DELETE FROM BOOKING");
	$db->query("DELETE FROM HOTEL_LOCATIONS");
    echo "Data cleared<br>";
    $districts = file_get_contents("district.csv");
    $districtArr = explode("\n",$districts);
    foreach ($districtArr as $district) {
        if(strlen($district)>1){
            $id = explode(";",$district)[0];
            $name = explode(";",$district)[1];
            $db->query("INSERT INTO DISTRICT VALUES($id,'$name')");
        }
    }
    echo "Districts inserted...<br>";
    $cities = file_get_contents("city.csv");
    $cityArr = explode("\n",$cities);
    foreach ($cityArr as $city) {
        if(strlen($city)>1){
            $id = explode(";",$city)[0];
            $did = explode(";",$city)[1];
            $name = explode(";",$city)[2];
            $db->query("INSERT INTO CITY VALUES($id,$did,'$name')");
        }
    }
    echo "Cities inserted...<br>";
    $names = file_get_contents("names.csv");
    $nameArr = explode("\n",$names);
    for ($i=1; $i <= 1620; $i++) { 
        $namesurname = GetRandomNameSurname($nameArr);
        $db->query("INSERT INTO CLIENT VALUES($i,'$namesurname[0]','$namesurname[1]')");
    }
    echo "Clients inserted...<br>";

    $db->query("INSERT INTO ROOMTYPE VALUES(1,'Single',100)");
    $db->query("INSERT INTO ROOMTYPE VALUES(2,'Double',150)");
    $db->query("INSERT INTO ROOMTYPE VALUES(3,'Suit',200)");
    $db->query("INSERT INTO ROOMTYPE VALUES(4,'Doublex',250)");
    $db->query("INSERT INTO ROOMTYPE VALUES(5,'King Suit',500)");
	echo "RoomTypes inserted...<br>";
	
    $hotels = ["Hilton Hotel","Limak Hotel","Marriott Hotel","Hyatt Hotel","Kaya Hotel","Dedeman Hotel","Rixos Hotel","Whyndam Hotel","Radisson Hotel","Crystal Hotel"];
	
	for($i=0; $i < 10; $i++){
		$db->query("INSERT INTO HOTEL VALUES($i + 1,'$hotels[$i]')");
	}
	
	$hotelsid=1;
	for ($i=1; $i <= 81; $i++) { 
        for($j=1; $j <= 5; $j++){
			$randomHotel = $hotels[rand(0,sizeof($hotels)-1)];
			$db->query("INSERT INTO HOTEL_LOCATIONS VALUES($hotelsid,$i,'$randomHotel')");
			$hotelsid++;
		}
    }
	echo "Hotels inserted...<br>";

    $facilties = ["Swimming Pool","Beach","Parking Lot","Spa","Bar"];
    $facilityid = 1;
    for ($i=1; $i <= 10; $i++) { 
        $randomFacility = $facilties[rand(0,sizeof($facilties)-1)];
        $db->query("INSERT INTO FACILITIES VALUES($facilityid,$i,'$randomFacility')");
        $facilityid++;
        $randomFacility = $facilties[rand(0,sizeof($facilties)-1)];
        $db->query("INSERT INTO FACILITIES VALUES($facilityid,$i,'$randomFacility')");
    }
    echo "Facilities inserted...<br>";

    $db->query("INSERT INTO TRAVELAGENT VALUES(1,'Booking','Website')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(2,'Trivago','Website')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(3,'Hotelz','Website')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(4,'RoomFind','Website')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(5,'BookRoom','Website')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(6,'ABC','Travel Agency')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(7,'Travellin','Travel Agency')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(8,'Tourisma','Travel Agency')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(9,'CCDF','Travel Agency')");
    $db->query("INSERT INTO TRAVELAGENT VALUES(10,'Agenca Travela','Travel Agency')");
    echo "Travel Agencies inserted...<br>";
    InsertData2();
    echo "Installation completed!";
}
function InsertData2()
{
    global $db;
    $roomid = 1;
    $sql="";
    for ($hotelid=1; $hotelid <= 10; $hotelid++) { 
        for ($k=0; $k < 10; $k++) { 
            for ($floor=0; $floor < 3; $floor++) { 
                $typeid = rand(1,5);
                $sql.=("INSERT INTO ROOM VALUES($roomid,$hotelid,$typeid,$floor);");
                $roomid++;
             }
        }
    }
    $db->multi_query($sql);
    echo "Rooms inserted...<br>";
    while ($db->next_result()) {;}
    $bookingid = 1;
    $sql="";
    for ($i=1; $i <= 1620 ; $i++) { 
        for ($j=0; $j < 3; $j++) { 
            $day = rand(1,31);
            $month = rand(1,12);
            $year = rand(2022,2024);
            $bdate = strval($year)."-".strval($month)."-".strval($day);
            $cidate = date("Y-m-d",strtotime($bdate . ' + '.(rand(1,5)." days")));
            $codate = date("Y-m-d",strtotime($cidate . ' + '.(rand(5,10)." days")));
            $r1 = rand(1,10);$r2 = rand(1,10);$r3 = rand(1,30);
            $bdate = date('Y-m-d',strtotime($bdate));
            $sql.=("INSERT INTO BOOKING VALUES($bookingid,'$bdate','$cidate','$codate',$r1,$i,$r2,$r3);");
            $bookingid++;
        }
    }
    $db->multi_query($sql);
    echo "Bookings inserted...<br>";
    while ($db->next_result()) {;}
}