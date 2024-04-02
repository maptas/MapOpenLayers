<script>
    try{
        <?php
            $machine="mars.alsim.com";
            $user="m.pochaud";
            $mdp="guido-van-rossum-is-my-master";
            $nombase="alsim";

            $connexion = new PDO('mysql:host=mars.alsim.com;dbname=alsim;', $user, $mdp);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

            //$reqString ! "SELECT * FROM tsocietes, tevenements where tsocietes.CAT_SOCIETE in ("OPERATEUR", "ACHETEUR") and tsocietes.PAYS_ADR in (1,2,3,4) and tevenements.id_societe = tsocietes.ID_SOCIETE and tevenements;"
            //reqString ! SELECT * FROM tsocietes where tsocietes.CAT_SOCIETE in ("OPERATEUR");
            //SELECT * FROM tsocietes, taddresses where tsocietes.CAT_SOCIETE in ("OPERATEUR") and tsocietes.ID_SOCIETE = taddresses.society and tsocietes.PAYS_ADR = taddresses.country

            $req = $connexion->prepare("SELECT tsocietes.NOM, taddresses.longitude, taddresses.longitude 
                                FROM tsocietes, taddresses 
                                where tsocietes.CAT_SOCIETE in (\"OPERATEUR\") and tsocietes.ID_SOCIETE = taddresses.society; ");
            $req->execute();

            $reqs = $req->fetchAll();
            foreach($reqs as $ligne){
                //echo $ligne['NOM'] . " / ";
            }
        ?>
        console.log("Connect to database");
    }catch{
        console.log("Connection refused");
    }
</script>
