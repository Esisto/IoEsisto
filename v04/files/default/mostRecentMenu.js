/*
 *
 *Script per modificare le classi del menu del widget mostRecent
 *
 */

$(document).ready(function() {
        $(".articlesBlock").hide();
        $(".articlesBlock:first").show();

        //rendo onUse la prima voce
        $(document.getElementById('liArticle0')).removeClass('firstMenuMostRecentList');
        $(document.getElementById('leftArticle0')).addClass('firstMenuMostRecentTabLeftUse');
        $(document.getElementById('centerArticle0')).addClass('menuMostRecentTabCenterUse');
        $(document.getElementById('rightArticle0')).addClass('menuMostRecentTabRightUse');


        $(".menuMostRecentTabCenter").click(function() {	
                var activeTab = $(this).find("a").attr("href");
                                                        
                if(activeTab == '#article0'){
                        var tab;
                        //formattando i li esterni
                        tab = $(this).find("a").attr("href").replace('#a','liA');
                        $(document.getElementById(tab)).removeClass('firstMenuMostRecentList');
                        $(document.getElementById('liArticle1')).addClass('menuMostRecentList');
                        $(document.getElementById('liArticle2')).addClass('menuMostRecentList');
                        $(document.getElementById('liArticle3')).addClass('menuMostRecentList');
                        
                        //formattando l'elemento precedentemente in uso
                        if($(document.getElementById('leftArticle1')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle1')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle1')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle1')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle2')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle2')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle2')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle2')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle3')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle3')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle3')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle3')).removeClass('menuMostRecentTabRightUse');
                        }
                        
                        //formattando l'elemento attivo
                        tab = $(this).find("a").attr("href");
                        $(document.getElementById(tab.replace('#a','leftA'))).addClass('firstMenuMostRecentTabLeftUse');
                        $(document.getElementById(tab.replace('#a','centerA'))).addClass('menuMostRecentTabCenterUse');
                        $(document.getElementById(tab.replace('#a','rightA'))).addClass('menuMostRecentTabRightUse');
                } else if(activeTab == '#article1'){
                        var tab;
                        //formattando i li esterni
                        tab = $(this).find("a").attr("href").replace('#a','liA');
                        $(document.getElementById(tab)).removeClass('menuMostRecentList');
                        $(document.getElementById('liArticle0')).addClass('firstMenuMostRecentList');
                        $(document.getElementById('liArticle2')).addClass('menuMostRecentList');
                        $(document.getElementById('liArticle3')).addClass('menuMostRecentList');
                        
                        //formattando l'elemento precedentemente in uso
                        if($(document.getElementById('leftArticle0')).hasClass('firstMenuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle0')).removeClass('firstMenuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle0')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle0')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle2')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle2')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle2')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle2')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle3')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle3')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle3')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle3')).removeClass('menuMostRecentTabRightUse');
                        }
                        
                        //formattando l'elemento attivo
                        tab = $(this).find("a").attr("href");
                        $(document.getElementById(tab.replace('#a','leftA'))).addClass('menuMostRecentTabLeftUse');
                        $(document.getElementById(tab.replace('#a','centerA'))).addClass('menuMostRecentTabCenterUse');
                        $(document.getElementById(tab.replace('#a','rightA'))).addClass('menuMostRecentTabRightUse');
                } else if(activeTab == '#article2'){
                        var tab;
                        //formattando i li esterni
                        tab = $(this).find("a").attr("href").replace('#a','liA');
                        $(document.getElementById(tab)).removeClass('menuMostRecentList');
                        $(document.getElementById('liArticle0')).addClass('firstMenuMostRecentList');
                        $(document.getElementById('liArticle1')).addClass('menuMostRecentList');
                        $(document.getElementById('liArticle3')).addClass('menuMostRecentList');
                        
                        //formattando l'elemento precedentemente in uso
                        if($(document.getElementById('leftArticle0')).hasClass('firstMenuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle0')).removeClass('firstMenuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle0')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle0')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle1')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle1')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle1')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle1')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle3')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle3')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle3')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle3')).removeClass('menuMostRecentTabRightUse');
                        }
                        
                        //formattando l'elemento attivo
                        tab = $(this).find("a").attr("href");
                        $(document.getElementById(tab.replace('#a','leftA'))).addClass('menuMostRecentTabLeftUse');
                        $(document.getElementById(tab.replace('#a','centerA'))).addClass('menuMostRecentTabCenterUse');
                        $(document.getElementById(tab.replace('#a','rightA'))).addClass('menuMostRecentTabRightUse');
                } else if(activeTab == '#article3'){
                        var tab;
                        //formattando i li esterni
                        tab = $(this).find("a").attr("href").replace('#a','liA');
                        $(document.getElementById(tab)).removeClass('menuMostRecentList');
                        $(document.getElementById('liArticle0')).addClass('firstMenuMostRecentList');
                        $(document.getElementById('liArticle1')).addClass('menuMostRecentList');
                        $(document.getElementById('liArticle2')).addClass('menuMostRecentList');
                        
                        //formattando l'elemento precedentemente in uso
                        if($(document.getElementById('leftArticle0')).hasClass('firstMenuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle0')).removeClass('firstMenuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle0')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle0')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle1')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle1')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle1')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle1')).removeClass('menuMostRecentTabRightUse');
                        } else if($(document.getElementById('leftArticle2')).hasClass('menuMostRecentTabLeftUse')){
                                $(document.getElementById('leftArticle2')).removeClass('menuMostRecentTabLeftUse');
                                $(document.getElementById('centerArticle2')).removeClass('menuMostRecentTabCenterUse');
                                $(document.getElementById('rightArticle2')).removeClass('menuMostRecentTabRightUse');
                        }
                        
                        //formattando l'elemento attivo
                        tab = $(this).find("a").attr("href");
                        $(document.getElementById(tab.replace('#a','leftA'))).addClass('menuMostRecentTabLeftUse');
                        $(document.getElementById(tab.replace('#a','centerA'))).addClass('menuMostRecentTabCenterUse');
                        $(document.getElementById(tab.replace('#a','rightA'))).addClass('menuMostRecentTabRightUse');
                }
                        
                $(".articlesBlock").hide();
                
                $(activeTab).fadeIn();
                return false;
        });
});