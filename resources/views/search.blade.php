<div class="row">
    <h4>Plenty of things to look for ...</h4>
    <form class="form-horizontal form-inline" method="post" action="">
        <div class="col-lg-3 form-group">
            <input class="form-control input-lg" type="text" placeholder="Search">
        </div>
        <div class="col-lg-2 form-group">
            <label class="radio-inline">
                <input type="radio" name="searchType" value="beer" id="searchTypeBeer" {{ old('searchType') == 'beer' ? 'checked' : '' }}>Beer
            </label>
            <label class="radio-inline">
                <input type="radio" name="searchType" value="brewery" id="searchTypeBrewery" {{ old('searchType') == 'brewery' ? 'checked' : '' }}>Brewery
            </label>
        </div>
        <div class="form-group col-lg-1">
            <button type="submit" class="btn btn-lg btn-info" id="doSearch">Find</button>
        </div>
    </form>
</div>