<!doctype html><html><head>
  <meta charset=utf8>
  <meta name=viewport content="width=device-width">
  <link rel=stylesheet href=css.css>
  <script src=jugadors.js></script>
</head><body>

<!--tria jugador-->
<div id=tria_jugador style=text-align:center;margin-bottom:5px>
  <select tipus=blanca></select>
  vs
  <select tipus=negra></select>
  <script>
    (function(){
      var selects=document.querySelectorAll('#tria_jugador select');
      for(var i=0;i<selects.length;i++){
        var select=selects[i];
        Jugadors.forEach(jug=>{
          var option=document.createElement('option');
          option.setAttribute('nom',jug.nom);
          option.innerHTML=jug.nom;
          option.value=jug.path;
          select.appendChild(option);
        });
        //onchange listener
        select.onchange=function(){
          var tipus=this.getAttribute('tipus');
          var fitxes=document.querySelectorAll('fitxa[tipus='+tipus+']');
          for(var i=0;i<fitxes.length;i++){
            fitxes[i].style.backgroundImage="url("+this.value+")";
          }
        }
      }

      //tria la laura per defecte a les blanques
      var s=document.querySelector('select[tipus=blanca]');
      s.value="img/laura.jpg";
      s.onchange();
    })();
  </script>
</div>

<!--taulell-->
<table id=taulell border=1>
  <?php for($i=0;$i<8;$i++){echo "<tr row=$i>";for($j=0;$j<8;$j++){echo "<td col=$j>";}}?>
</table>

<script>
  //get cela by x,y
  function getCela(x,y){return document.querySelector('#taulell tr[row="'+x+'"] td[col="'+y+'"]');}

  //get fitxa tipus, numero
  function getFitxa(tipus,numero){return document.querySelector('fitxa[tipus='+tipus+'][numero="'+numero+'"]');}

  //comprova quina fitxa hi ha a la cela x,y (null per defecte)
  function comprovaCela(x,y){return getCela(x,y).querySelector('fitxa');}

  //ressalta un array de celes
  function ressaltaCeles(celes){
    //reset a les ja ressaltades
    var antigues=document.querySelectorAll('#taulell td.ressaltada')
    for(var i=0;i<antigues.length;i++){
      antigues[i].classList.remove('ressaltada');
      antigues[i].onclick=null;
    }
    //ressalta array de celes
    celes.forEach(c=>{c.classList.add('ressaltada')});
  }

  //comprova si una fitxa es reina
  function isReina(fitxa){if(!fitxa){return false}return fitxa.classList.contains('reina');}

  //mou fitxa a destí x,y
  function mouFitxa(fitxa,cela){
    //agafa coordenades per trobar la fitxa a matar
    var ori_x=parseInt(fitxa.parentNode.parentNode.getAttribute('row'));
    var ori_y=parseInt(fitxa.parentNode.getAttribute('col'));
    var des_x=parseInt(cela.parentNode.getAttribute('row'));
    var des_y=parseInt(cela.getAttribute('col'));
    var diff=Math.abs(ori_x-des_x);
    if(diff>1){
      var f_x=Math.min(ori_x,des_x)+1;
      var f_y=Math.min(ori_y,des_y)+1;
      console.log("ori: "+ori_x+","+ori_y)
      console.log("des: "+des_x+","+des_y)
      console.log("mat: "+f_x+","+f_y)
      mataFitxa(comprovaCela(f_x,f_y));
    }

    //mou
    cela.appendChild(fitxa);
    //fes reina la fitxa
    if(fitxa.getAttribute('tipus')=='blanca' && cela.parentNode.getAttribute('row')=="0"){fesReina(fitxa)}
    if(fitxa.getAttribute('tipus')=='negra'  && cela.parentNode.getAttribute('row')=="7"){fesReina(fitxa)}
  }

  //fes reina una fitxa
  function fesReina(fitxa){if(fitxa)fitxa.classList.add('reina')}

  //mata fitxa
  function mataFitxa(fitxa){if(fitxa)fitxa.parentNode.removeChild(fitxa);}

  //comprova els moviments possibles des de la cela d'origen (retorna celes de destí)
  function celesPossibles(fitxa){
    //fitxa origen
    var x=parseInt(fitxa.parentNode.parentNode.getAttribute('row'));
    var y=parseInt(fitxa.parentNode.getAttribute('col'));
    //llista de celes de destí
    var destins=[];
    //tipus de la fitxa origen
    var tipus=fitxa.getAttribute('tipus');
    var is_reina=isReina(fitxa);
    //si la fitxa a l'origen és blanca, mira a dalt, si és negra, mira a baix, si es reina, a dalt i a baix
    if(tipus=='blanca' || is_reina){
      //1. dalt
      var xd=x-1;
      //1.1 esquerra
      var yd=y-1;
      var candidata=getCela(xd,yd);
      //comprova moviment normal
      if(candidata && !comprovaCela(xd,yd)){destins.push(candidata);}
      //comprova si pots matar
      else if(candidata && comprovaCela(xd,yd)){ 
        var candidata=getCela(xd-1,yd-1); //comprova si cela buida
        if(candidata && !comprovaCela(xd-1,yd-1)) destins.push(candidata);
      }
      //1.2 dreta
      var yd=y+1;
      var candidata=getCela(xd,yd);
      //comprova moviment normal
      if(candidata && !comprovaCela(xd,yd)){destins.push(candidata);}
      //comprova si pots matar
      else if(candidata && comprovaCela(xd,yd)){ 
        var candidata=getCela(xd-1,yd+1); //comprova si cela buida
        if(candidata && !comprovaCela(xd-1,yd+1)) destins.push(candidata);
      }
    }
    if(tipus=='negra' || is_reina){
      //2. baix
      var xd=x+1;
      //2.1 esquerra
      var yd=y-1;
      var candidata=getCela(xd,yd);
      //comprova moviment normal
      if(candidata && !comprovaCela(xd,yd)){destins.push(candidata);}
      //comprova si pots matar
      else if(candidata && comprovaCela(xd,yd)){ 
        var candidata=getCela(xd+1,yd-1); //comprova si cela buida
        if(candidata && !comprovaCela(xd+1,yd-1)) destins.push(candidata);
      }
      //2.2 dreta
      var yd=y+1;
      var candidata=getCela(xd,yd);
      //comprova moviment normal
      if(candidata && !comprovaCela(xd,yd)){destins.push(candidata);}
      //comprova si pots matar
      else if(candidata && comprovaCela(xd,yd)){ 
        var candidata=getCela(xd+1,yd+1); //comprova si cela buida
        if(candidata && !comprovaCela(xd+1,yd+1)) destins.push(candidata);
      }
    }
    return destins;
  }

  //selecciona fitxa
  function seleccionaFitxa(fitxa){
    //desressalta altres
    ressaltaCeles([]);
    //deselecciona les altres
    var fitxes=document.querySelectorAll('fitxa.seleccionada');
    for(var i=0;i<fitxes.length;i++){
      fitxes[i].classList.remove('seleccionada');
    }
    //comprova la fitxa seleccionada
    if(!fitxa)return;
    //posa la classe seleccionada
    fitxa.classList.add('seleccionada');
    //ressalta possibles moviments de la fitxa
    var destins=celesPossibles(fitxa);
    ressaltaCeles(destins);
    //posa listeners
    destins.forEach(d=>{
      d.onclick=function(){
        mouFitxa(fitxa,d);
        seleccionaFitxa(false);
      };
    });
  }
</script>

<script>
  //inicialització
  function creaFitxa(x,y,tipus,numero){
    var f=document.createElement('fitxa');
    f.setAttribute('tipus',tipus);
    f.setAttribute('numero',numero);
    getCela(x,y).appendChild(f);
  }
  (function creaNegres(){
    creaFitxa(0,1,'negra',0);
    creaFitxa(0,3,'negra',1);
    creaFitxa(0,5,'negra',2);
    creaFitxa(0,7,'negra',3);
    creaFitxa(1,0,'negra',4);
    creaFitxa(1,2,'negra',5);
    creaFitxa(1,4,'negra',6);
    creaFitxa(1,6,'negra',7);
    creaFitxa(2,1,'negra',8);
    creaFitxa(2,3,'negra',9);
    creaFitxa(2,5,'negra',10);
    creaFitxa(2,7,'negra',11);
  })();
  (function creaBlanques(){
    creaFitxa(5,0,'blanca',0);
    creaFitxa(5,2,'blanca',1);
    creaFitxa(5,4,'blanca',2);
    creaFitxa(5,6,'blanca',3);
    creaFitxa(6,1,'blanca',4);
    creaFitxa(6,3,'blanca',5);
    creaFitxa(6,5,'blanca',6);
    creaFitxa(6,7,'blanca',7);
    creaFitxa(7,0,'blanca',8);
    creaFitxa(7,2,'blanca',9);
    creaFitxa(7,4,'blanca',10);
    creaFitxa(7,6,'blanca',11);
  })();
  //posa listeners onclick=seleccionaFitxa(this)
  (function(){
    var fitxes=document.querySelectorAll('fitxa');
    for(var i=0;i<fitxes.length;i++){
      fitxes[i].onclick=function(){
        seleccionaFitxa(this);
      }
    }
  })();
</script>

<!--footer-->
<footer style="margin-top:5px;padding:0.3em;text-align:center;font-size:10px;background:#eee">
  <div><i>Feliç any 2000! &#128157;</i></div>
  <div>
    Tites i tetes software © Copyright 1989-2017
  </div>
  <div>
    <a href="///github.com/holalluis/dames2000">Codi font</a>
  </div>
</footer>
