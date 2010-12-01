    google.load("search", "1");

    function OnLoad() {
      // Create a search control
      var searchControl = new google.search.SearchControl();

      // Add in a full set of searchers
      var localSearch = new google.search.LocalSearch();
      //searchControl.addSearcher(localSearch);
      // site restricted web search using a custom search engine
      siteSearch = new google.search.WebSearch();
      siteSearch.setUserDefinedLabel("Publichi");
      siteSearch.setSiteRestriction("it.wikipedia.org");
      searchControl.addSearcher(siteSearch);
      //searchControl.addSearcher(new google.search.VideoSearch());
      //searchControl.addSearcher(new google.search.BlogSearch());

      // Set the Local Search center point
      localSearch.setCenterPoint("New York, NY");

      // Tell the searcher to draw itself and tell it where to attach
      searchControl.draw(document.getElementById("searchcontrol"));

      // web search, open, alternate root
//      var options = new google.search.SearcherOptions();
//      options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
//      options.setRoot(document.getElementById(""));
//      searchControl.addSearcher(new google.search.WebSearch(), options);
      
      // Execute an inital search
      //searchControl.execute("Google");
    }
    google.setOnLoadCallback(OnLoad);
