<!DOCTYPE html>
<html lang="en">
  <!-- Ce que je veux faire pour la page d'information des entreprises :
      Il faudrait que lors d'un click sur un icône, un popup s'affiche (voir exemple openlayers 'Geographic coordinates' dans historique et essai plus bas) 
        avec le nom et tel, siteWEB, addresse, commentaire, ficheIntranet (dans lien des fiches intranets se trouve l'id de société).              <<<<<<---------------------
          'com':'".str_replace($remplace, " ", $line['COMMENTAIRE'])."', (tsocietes.COMMENTAIRE)
      Ajouter à la place du bouton recherché, un panel (20% à droite) avec une recherche et une liste de société, si l'on clique dessus, la vue centre dessus et ouvre le popup.
      D'ailleurs, le full screen marche plus car ajout de lui en parent de la map pour test css.
  -->
  <head>
    <link rel="stylesheet" href="style.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <?php
      include 'connexion.php';
    ?>
    <script src="./ol.js" type="text/javascript"></script>

    <!--------------Requête SQL pour Filtres-------------->
    <script>
      var tabDepart = [];
      var tabRech = [];
      var tabDate = [];
      var tabFin = tabDepart;
      var lat = -1.553621;
      var lon = 47.218371;
      const currrentDate = new Date();
      const currrentYear = currrentDate.getFullYear();
      const currrentMonth = currrentDate.getMonth();
      const currrentDay = currrentDate.getDate();

      <?php
        echo "console.log('Version PHP : ".phpversion()."');\n";
        $remplace = array("'", "\\", "<br>", "\n", "\r");

        //// Requête des noms de pays
        $reqPays = $connexion->prepare("SELECT tpays.NOM FROM tpays WHERE LENGTH(tpays.NOM) > 0; ");
        $reqPays->execute();
        $reqsPays = $reqPays->fetchAll();
        $tabPays = array();
        foreach($reqsPays as $ligne){
          array_push($tabPays, $ligne[0]);
        }
        array_push($tabPays, 'TOUT');

        //// Requête des noms de catégories
        $reqCat = $connexion->prepare("SELECT tsocietes.CAT_SOCIETE FROM tsocietes group by CAT_SOCIETE; ");
        $reqCat->execute();
        $reqsCat = $reqCat->fetchAll();
        $tabCat = array();
        foreach($reqsCat as $ligne){
          array_push($tabCat, $ligne[0]);
        }
        array_push($tabCat, 'TOUT');

        //// Requête des noms d'Alsimiens
        $reqComm = $connexion->prepare("SELECT * FROM `tsalaries` WHERE `DATE_DEPART` is null and TITRE != 'VIRTUEL'; ");
        $reqComm->execute();
        $reqsComm = $reqComm->fetchAll();
        $tabCommNom = array();
        $tabCommPrenom = array();
        array_push($tabCommNom, 'Tous');
        array_push($tabCommPrenom, 'les commerciaux');
        foreach($reqsComm as $ligne){
          array_push($tabCommNom, $ligne['NOM']);
          array_push($tabCommPrenom, $ligne['PRENOM']);
        }

        //// Requête du dernier contact entre la société et Alsim
        $reqDate = $connexion->prepare("SELECT id_personne_alsim, id_societe, date FROM `tevenements` GROUP BY id_societe ORDER BY date; ");
        $reqDate->execute();
        foreach($reqDate as $line){
          echo "tabDate.push({'idPersonneAlsim':".$line['id_personne_alsim'].",'idSociete':'".$line['id_societe']."', 'date':'".$line['date']."'});";
        }

        //// Requête générale des noms de sociétés et informations complémentaires
        $T = "SELECT tsocietes.NOM, taddresses.longitude, taddresses.latitude, tsocietes.CAT_SOCIETE, 
                tpays.NOM as pays, tsocietes.TEL, tsocietes.ID_SOCIETE,
                taddresses.ADDRESS_1, taddresses.ADDRESS_2, taddresses.ADDRESS_3, tsocietes.SITE_INTERNET,
                tsalaries.NOM as NomResp, tsalaries.PRENOM as PrenomResp
                FROM tsocietes, taddresses, tpays, tsalaries 
                where tsocietes.ID_SOCIETE = taddresses.society and taddresses.country = tpays.ID 
                  and tsocietes.ID_RESPONSABLE_ALSIM = tsalaries.ID_PERSONNE
                  and taddresses.longitude is not null and taddresses.latitude is not null 
                  and tsocietes.CAT_SOCIETE is not null
                  and LENGTH(tpays.NOM) > 0 and tpays.NOM is not null
                group by tsocietes.NOM; ";
        $reqDepart = $connexion->prepare($T);
        $reqDepart->execute();
        foreach($reqDepart as $line)
        {
          echo "tabDepart.push({'lat':".$line['longitude'].", 'lon':".$line['latitude'].", 'id':".$line['ID_SOCIETE'].",
                'label':'".str_replace($remplace, " ", $line['NOM'])."', 'cat':'".$line['CAT_SOCIETE']."', 
                'pays':'".str_replace($remplace, " ",$line['pays'])."', 'tel':'".$line['TEL']."', 
                'siteW':'".$line['SITE_INTERNET']."', 
                'ad1':'".str_replace($remplace, " ", $line['ADDRESS_1'])."', 
                'ad2':'".str_replace($remplace, " ", $line['ADDRESS_2'])."', 
                'ad3':'".str_replace($remplace, " ", $line['ADDRESS_3'])."',
                'nomResp':'".$line['NomResp']."',
                'prenomResp':'".$line['PrenomResp']."'
              });";
        }
      ?>
      var iJsp = 0;
      tabDepart.forEach(lline =>{
        if(line.id == tabDate[i].idSociete){
        }
      })
        // if($pays == 'TOUT' && $categorie == 'TOUT')
        // {
          
        //   $req = $connexion->prepare("SELECT tsocietes.NOM, taddresses.longitude, taddresses.latitude 
        //                               FROM tsocietes, taddresses
        //                               where tsocietes.ID_SOCIETE = taddresses.society and taddresses.longitude is not null 
        //                               and taddresses.latitude is not null; ");
        //   $req->execute();
        // }
        // elseif($pays == 'TOUT')
        // {
        //   $req = $connexion->prepare("SELECT tsocietes.NOM, taddresses.longitude, taddresses.latitude 
        //                             FROM tsocietes, taddresses 
        //                             where tsocietes.CAT_SOCIETE = '$categorie' and tsocietes.ID_SOCIETE = taddresses.society and taddresses.longitude is not null 
        //                               and taddresses.latitude is not null; ");
        //   $req->execute();
        // }
        // elseif($categorie == 'TOUT')
        // {
        //   $req = $connexion->prepare("SELECT tsocietes.NOM, taddresses.longitude, taddresses.latitude 
        //                               FROM tsocietes, taddresses, tpays 
        //                               where tsocietes.ID_SOCIETE = taddresses.society and taddresses.country = tpays.ID and tpays.NOM = '$pays' and taddresses.longitude is not null 
        //                               and taddresses.latitude is not null; ");
        //   $req->execute();
        // }
        // else
        // {
        //   $T = "SELECT tsocietes.NOM, taddresses.longitude, taddresses.latitude 
        //         FROM tsocietes, taddresses, tpays 
        //         where tsocietes.CAT_SOCIETE = '$categorie' and tsocietes.ID_SOCIETE = taddresses.society and taddresses.country = tpays.ID and tpays.NOM = '$pays' and taddresses.longitude is not null 
        //                               and taddresses.latitude is not null;";
        //   $req = $connexion->prepare($T);
        //   $req->execute();
        // }
        // $reqs = $req->fetchAll();
        // foreach($reqs as $line)
        // {
        //   echo "tabDepart.push({'lat':".$line['longitude'].", 'lon':".$line['latitude'].
        //         ", 'label':'".str_replace($remplace, " ", $line['NOM'])."'});";
        //   echo "\n";
        // }
      // ?>

        /******* Fonction JavaScript *******/

      var expre = "";
      function ExpressionRegu(name, post){
        //expre = expre + post;
        return name.includes(expre);
      }

      function EmptyList(){
        var options = document.querySelectorAll('#tabSociete option');
        //expre = "";
        options.forEach(o => o.remove());
      }

      function CreateFeatures(tab){
        var tabFeat = [];
        var index = 0;
        tab.forEach(line =>{
          var feat = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([line.lat,line.lon])),
            name: line.label
          });
          feat.setId(index++);
          ChangeIconByCat(line, SearchIndexCat(line), feat);
          //ChangeColorByLevelPros(line, SearchIndexCat(line), IfExist(line), feat);
          console.log("IfExist : "+IfExist(line));

          // feat.setStyle(new ol.style.Style({
          //   image: new ol.style.Icon({
          //     src:'https://ressource.arthurbazin.com/demo/open_layer/ressource/placeholder_black.svg',
          //     scale:0.5,
          //     rotateWithView: true,
          //   }),
          //   text: new ol.style.Text({
          //     text: line.label,
          //     offsetY: 19,
          //     rotateWithView:true,
          //     stroke: new ol.style.Stroke({
          //       color:[255, 255, 255, 0.7],
          //       width:3,
          //     }),
          //   }),
          // }));
          tabFeat.push(feat);
        });
        vectorSource.addFeatures(tabFeat); 
      }

      function UpdateTabFiltre(){
        tabFin = [];
        var selectPays = document.getElementById("tabPaysSelect");
        var selectCategorie = document.getElementById("tabCategorieSelect");
        var selectCommercial = document.getElementById("tabCommerceSelect");
        var valuePays = selectPays.options[selectPays.selectedIndex].text;
        var valueCategorie = selectCategorie.options[selectCategorie.selectedIndex].text;
        var valueCommerce = selectCommercial.options[selectCommercial.selectedIndex].text;

        tabDepart.forEach(line =>{
          var nomPrenom = line.nomResp+" "+line.prenomResp;
          if(valuePays == 'TOUT' && valueCategorie == 'TOUT' && valueCommerce == 'Tous les commerciaux'){
            tabFin.push(line);
          }
          else if(valuePays == 'TOUT' && valueCategorie == 'TOUT'){
            console.log("juste PTout et line Cat : "+line.cat);
            if(nomPrenom == valueCommerce){
              tabFin.push(line);
            }
          }
          else if(valuePays == 'TOUT' && valueCommerce == 'Tous les commerciaux'){
            if(line.cat == valueCategorie){
              tabFin.push(line);
            }
          }
          else if(valueCategorie == 'TOUT' && valueCommerce == 'Tous les commerciaux'){
            if(line.pays == valuePays){
              tabFin.push(line);
            }
          }
          else if(valueCategorie == 'TOUT'){
            if(line.pays == valuePays && nomPrenom == valueCommerce){
              tabFin.push(line);
            }
          }
          else if(valuePays == 'TOUT'){
            if(line.cat == valueCategorie && nomPrenom == valueCommerce){
              tabFin.push(line);
            }
          }
          else if(valueCommerce == 'Tous les commerciaux'){
            if(line.cat == valueCategorie && line.pays == valuePays){
              tabFin.push(line);
            }
          }
          else if(line.pays == valuePays && line.cat == valueCategorie && nomPrenom == valueCommerce){
            tabFin.push(line);
          }
        });
        RefreshVector(tabFin);
        RefreshListMap();
      }

      function RefreshListMap(){
        document.getElementById('tabSociete').options.length=0;
        var selectCtrl = document.getElementById("tabSociete");
        tabRech = tabFin;
        tabRech.forEach(line =>{
          if(ExpressionRegu(line.label, expre)){
            var el = document.createElement("option");
            el.textContent = line.label;
            el.value = "'"+line.label+"'";
            el.id = "optionSociete";
            selectCtrl.appendChild(el);
          }
        })
      }

      function RefreshVector(tab){
        vectorSource.refresh();
        CreateFeatures(tab);
        console.log(tab);
      }

      function NoDouble(tab, line){
        if(line == tab){
          return true;
        }
        else{
          return false;
        }
      }

      function UpdateListMap(){
        EmptyList();
        var i = 0;
        tabRech = [];
        var selectResh = document.querySelector("#champsRecherche input");
        var selectLetter = document.getElementById("champsRecherche").value;
        expre = selectLetter;
        console.log("je passe dans la fonction avec un "+expre);
        var selectCtrl = document.getElementById("tabSociete");
        tabFin.forEach(line =>{
          i++;
          if(ExpressionRegu(line.label, expre)){
            console.log("Line : "+line.label)

            //// ajout liste
            var el = document.createElement("option");
            el.textContent = line.label;
            el.value = "'"+line.label+"'";
            el.id = "optionSociete";
            selectCtrl.appendChild(el);
            tabRech.push(line);
          }
        })
        RefreshVector(tabRech);
      }

      function ChangeView(){
        select = document.getElementById("tabSociete");
        valueSelect = select.options[select.selectedIndex].text;
        console.log("SELECTION : "+valueSelect);
        tabDepart.forEach(line =>{
          if(valueSelect == line.label){
            lat = line.lat;
            lon = line.lon;
          }
        })
        console.log("lat : "+lat+" / lon : "+lon);
        map.setView(new ol.View({
          center: ol.proj.fromLonLat([lat,lon]),
          resolution: 432,
          zoom: 5,
        }));
      }

      function IfExist(line){
        tabDate.forEach(lineDate =>{
          if(lineDate.idSociete == line.id){
            var iColor = IndexColorByDate(lineDate);
            console.log("fonction d I: "+iColor);
            return iColor;
          }
        })
      }
      function IfExist(){
        var i =0;
        tabDepart.forEach(line =>{
          //if(line.id == tabDate[i].idSociete){
            tabDepart['colorIndex'] = 0;
          //}
          i++;
        })
      }

      function IndexColorByDate(lineDate){
        var annee = parseInt(lineDate.date.slice(0,3));
        var mois = parseInt(lineDate.date.slice(5,6));
        if(annee > currrentYear-1){
          if(mois > currrentMonth-1){
            return 0;
          }
          else if(mois > currrentMonth-3){
            return 1;
          }
          else{
            return 2;
          }
        }
        else{
          return 2;
        }
      }

      var tabImageOfCat = {
        "OPERATEUR" : './img/PinMapOperateur/PinMapOperateur',
        "ACHETEUR" : './img/PinMapAcheteur/PinMapAcheteur',
        "PROSPECT" : './img/PinMapProspect/PinMapProspect',
        "DISTRIBUTEUR" : './img/PinMapDistributeur/PinMapDistributeur',
        "FOURNISSEUR" : './img/PinMapFournisseur/PinMapFournisseur',
        "PARTENAIRE" : './img/PinMapPartenaire/PinMapPartenaire',
        "PROSPECT DISPARU" : './img/PinMapProspectDisparu/PinMapProspectDisparu',
        "OPERATEUR DISPARU" : './img/PinMapOperateurDisparu/PinMapOperateurDisparu',
        "CONCURRENT" : './img/PinMapConcurrent/PinMapConcurrent',
      };
      var tabImageColor = ["Vert", "Orange", "Rouge"];

      function SearchIndexCat(line){
        if(line.cat == "OPERATEUR")return 0;
        else if(line.cat == "ACHETEUR")return 1;
        else if(line.cat == "PROSPECT")return 2;
        else if(line.cat == "DISTRIBUTEUR")return 3;
        else if(line.cat == "FOURNISSEUR")return 4;
        else if(line.cat == "PARTENAIRE")return 5;
        else if(line.cat == "PROSPECT DISPARU")return 6;
        else if(line.cat == "OPERATEUR DISPARU")return 7;
        else if(line.cat == "CONCURRENT")return 8;
      }

      function ChangeIconByCat(line, iImage, feat){
        tabImg = ['Operateur', 'Acheteur', 'Prospect', 'Distributeur', 'Fournisseur', 'Partenaire', 'ProspectDisparu', 'OperateurDisparu', 'Concurrent'];
        var urlImgCat = './img/imgConstruc/PinMap'+tabImg[iImage]+'.png';
        var StyleCat = new ol.style.Style({
          image: new ol.style.Icon({
            src: urlImgCat,
            scale:0.1,
            rotateWithView: true,
          }),
          text: new ol.style.Text({
            text: line.label,
            offsetY: 19,
            rotateWithView: true,
            stroke: new ol.style.Stroke({
              color:[255, 255, 255, 0.7],
              width:3,
            }),
          }),
          stroke: new ol.style.Stroke({
            width: 0.5,
          }),
        });
        feat.setStyle(StyleCat);
      }

      function ChangeColorByLevelPros(line, iImage, iColor, feat){
        var key = Object.keys(tabImageOfCat)[iImage];
        var value = tabImageOfCat[key];
        var urlImg = value+tabImageColor[iColor]+'.png';
        var StyleCat = new ol.style.Style({
          image: new ol.style.Icon({
            src: urlImg,
            scale:0.1,
            rotateWithView: true,
          }),
          text: new ol.style.Text({
            text: line.label,
            offsetY: 19,
            rotateWithView: true,
            stroke: new ol.style.Stroke({
              color:[255, 255, 255, 0.7],
              width:3,
            }),
          }),
        });
        feat.setStyle(StyleCat);
      }

    </script>

      <!------ Style CSS ------>

    <style>
      html, body {
        margin: 0;
        height: 100%;
        background-color: #1F6B75;
      }
      #map {
        /*  position: absolute;*/
        top: 0;
        bottom: 0;
        width: 80%;
        height: 85%;
        float: left;
      }
      /*Essaie avec création d'un sidePanel et affichage sur 20% de la largeur de la page mais nul, il faudrait que le sidePanel recouvre 20% de la carte, sans réduire la taille de la carte.*/ 
      #sidepanel {
        background-color: #1F6B75;
        width: 20%;
        height: 100%;
        float: right;
        border-color: #000;
        border-width: thick;
        /*display: none;/*enlever pour afficher + mettre 80% width de la map.*/
      }
      #sidepaneltile {
        width: 100%;
        font-size: 2em;
        color: #FFF;
        display: block;
        text-align: center;
      }
      h1 {
        color: #FFF;
        text-align: center;
      }
      #resultRech {
        color: #FFF;
      }
      #champsRecherche {
        margin: 15px auto 15px;
        width: 100%;
      }
      #tabSociete {
        width: 100%;
      }
      #btnTabSociete {
        margin: 5px 70px;
        width: 50%;
        background-color: #04dbb4;
        border-radius: 30px;
        border: none;
      }
      .formuFiltre{
        margin: 5px;
        background-color: #04dbb4;
        border: none;
        border-radius: 8px;
      }
      input.formuFiltre{
        border-radius: 15px;
        width: 15%;
      }
      td {
        padding: 0 0.5em;
        text-align: right;
      }
      th {
        width: 150px;
        text-align: left;
      }
      .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 380px;
      }
      .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }
      .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
      }
      .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
      }
      .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
      }
      .ol-popup-closer:after {
        content: "✖";
      }
    </style>

      <!------ HTML de la page WEB ------>

    <title>Companies Map</title>
    <meta charset="utf-8"></meta>
  </head>
  <body>
    <h1>Companies Map</h1>

    <select name="comboCat" class="formuFiltre" id="tabCategorieSelect">
      <?php
        for ($iCat=0; $iCat<Count($tabCat); $iCat++){
          echo "<option value='$tabCat[$iCat]' id='optionCategorie' onclick='UpdateTabFiltre()'>$tabCat[$iCat]</option>";
        }
      ?>
    </select>

    <select name="comboPays" class="formuFiltre" id="tabPaysSelect">
      <?php
        for ($iPays=1; $iPays< count($tabPays); $iPays++){
          echo "<option value='$tabPays[$iPays]' id='optionPays' onclick='UpdateTabFiltre()'>$tabPays[$iPays]</option>";
        }
      ?>
    </select>

    <select name="comboComm" class="formuFiltre" id="tabCommerceSelect">
      <?php
        for ($iComm=0; $iComm< count($tabCommNom); $iComm++){
          echo "<option value='$tabCommNom[$iComm]' id='optionComm' onclick='UpdateTabFiltre()'>$tabCommNom[$iComm] $tabCommPrenom[$iComm]</option>";
        }
      ?>
    </select>

    <div id="map"></div>
    <div id="popup" class="ol-popup">
      <a href="#" id="popup-closer" class="ol-popup-closer"></a>
      <div id="info"></div>
    </div>

    <div id="sidepanel">
      <input id="champsRecherche" name="champsRecherche" placeholder="Rechercher une société" oninput="UpdateListMap()"/>
      <select name="nomSociete" id="tabSociete" size="18" onclick="ChangeView()">
        <?php
          foreach($reqs as $line){
            echo "<option id='optionSociete' value=".$line['NOM'].">".$line['NOM']."</option>";
          }
        ?>
      </select>
    </div>

    <!-- Script JavaScript de création de la carte -->

    <script type="text/javascript">

      /*****Création des interactions*****/

      var cont_echelle = new ol.control.ScaleLine({})

      var cont_barre_zoom = new ol.control.ZoomSlider({})

      var cont_position_curseur = new ol.control.MousePosition({	
        coordinateFormat: function(coordinate) {
          return ol.coordinate.format(coordinate, '<span><i class="fas fa-map-marker-alt"></i> {x} ° | {y} °</span>', 6);
        },
        projection: 'EPSG:4326',
      })
      
      var cont_plein_ecran = new ol.control.FullScreen({
        tipLabel: 'Passez en mode plein-écran',
      })

      var cont_zoom = new ol.control.Zoom({
        zoomInTipLabel: 'Zoomer',
        zoomOutTipLabel: 'Dézoomer',
      })

      var cont_mention_legale = new ol.control.Attribution({
        collapsible: true,
        collapsed: true,
        tipLabel: 'Attributions',
      })

      /***** Création de la carte, des couches et vecteurs *****/

      var osmLayer = new ol.layer.Tile({
        source: new ol.source.OSM(),
        title: 'OSM',
        type: 'base',
      })

      var vectorSource = new ol.source.Vector({
        features: new ol.format.GeoJSON().readFeatures(
          {
            'type': 'FeatureCollection',
            'features': []
          },
          {featureProjection: 'EPSG:3857'},
        )
      })

      CreateFeatures(tabDepart); 

      var vectorLayer = new ol.layer.Vector({
        title: 'Couche vector',
        source: vectorSource,
        updateWhileInteracting: true
      });

      var vueCarte = new ol.View({
        center: ol.proj.fromLonLat([lat,lon]),
        resolution: 432,
        zoom: 0,
        maxZoom: 20
      })

      var map = new ol.Map({
        target: 'map',
        layers: [osmLayer, vectorLayer],
        view: vueCarte,
        controls: [
          cont_zoom,
          cont_echelle,
          cont_position_curseur,
          cont_plein_ecran,
          cont_barre_zoom,
          cont_mention_legale,
        ],
      });

      /***** Création des interactions au Clique d'un utilisateur *****/

      var styleInteraction = new ol.style.Style({
        image: new ol.style.Circle({
          radius: 5,
          fill: new ol.style.Fill({
            color: '#FF0000'
          }),
          stroke: new ol.style.Stroke({
            color: '#000000',
            width: 1.25
          })
        })
      })

      var selectClick = new ol.interaction.Select({
        style: styleInteraction,
      });

      const container = document.getElementById('popup');
      const info = document.getElementById('info');
      const closer = document.getElementById('popup-closer');
      
      const popup = new ol.Overlay({
        element: container,
        stopEvent: false,
        autoPan:{
          animation:{
            duration: 250,
          }
        },
        positioning: 'bottom-center',
      });
      closer.onclick = function(){
        popup.setPosition(undefined);
        closer.blur();//enlève le focus
        return false;
      }
      map.addOverlay(popup);

      function InfoPopup(nom, tel, siteWEB, ad1, ad2, ad3, ficheIntranet) {
        return `
          <table>
            <tbody>
              <tr><th>Nom : </th><td>${nom}</td></tr>
              <tr><th>Téléphone : </th><td>${tel}</td></tr>
              <tr><th>Adresse : </th><td>${ad1}</td></tr>
            </tbody>
          </table>
          <p><a href="http://${siteWEB}">${siteWEB}</a></p>
          <p><a href="http://${siteWEB}">FIche Intranet</a></p>`;
      }

      map.addInteraction(selectClick);

      selectClick.on('select', function(e)
      {
        //e.selected.forEach (selectedFeature => selectedFeature.setStyle(styleInteraction)) 
        var index = -1;
        e.selected.forEach (selectedFeature => {
          console.log(selectedFeature.getId());
          index = selectedFeature.getId();
        });

        if (index==-1) 
          return;

        var latSelectedF = tabFin[index].lat;
        var lonSelectedF = tabFin[index].lon;
        var nomSelectedF = tabFin[index].label;
        var coord = ol.proj.fromLonLat([latSelectedF,lonSelectedF]);
        console.log("coord : "+coord);
        console.log("lat : "+tabFin[index].lat);
        console.log("lon : "+tabFin[index].lon);

        var nom = tabFin[index].label;
        var tel = tabFin[index].tel;
        var siteWEB = tabFin[index].siteW;
        var ad1 = tabFin[index].ad1;
        var ad2 = tabFin[index].ad2;
        var ad3 = tabFin[index].ad3;
        var ficheIntranet = "Fiche Intranet bientôt disponible";
        info.innerHTML = InfoPopup(nom, tel, siteWEB, ad1, ad2, ad3, ficheIntranet);
        popup.setPosition(coord);//Change to point coordinates.
      })
    </script>
  </body>
</html>