<div class="box">
           <h2>Categoria</h2>
            <?
            if (isset($risultato_cpv) && $risultato_cpv->rowCount()>0) {
                    while($rec_categorie = $risultato_cpv->fetch(PDO::FETCH_ASSOC)) {
                            $lista = "inseriti";
                            include("categorie/categoria.php");
                    }
            }
            ?>
</div>
