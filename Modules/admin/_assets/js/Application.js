/**
 * Created by musaatalay on 29.10.2014.
 */

var Application = function(e){

    var This = this;

    this.e = $(e) || {};

    this.notif = function(o){

        var Settings = o || {};

        var CallBack = Settings.callback || function(){};

        var Subtext = Settings.message.subtext || null;

        var Icon = "icon-" + Settings.icon.icon || "icon-ok-sign";

        var IconSize = "icon-" + Settings.icon.size || null;

        var IconColor =  Settings.icon.color || "green";

        var Bildirim = '<div class="notif hide"><span class="'+IconColor+' icon '+Icon+' '+IconSize+'"></span><p><strong>'+Settings.message.header+'</strong>'+Subtext+'</p></div>';

        var BildirimAlani = this.e;

        BildirimAlani.html('<div class="notif"><span style="border: none !important;" class="mini icon icon-circleselection spinning"></span><p><strong>İşleminiz yürütülüyor ...</strong>Lütfen Bekleyiniz</p></div>');

        setTimeout(function(){

            BildirimAlani.find(".notif").addClass("hide", function(){

                setTimeout(function(){

                    BildirimAlani.html(Bildirim).find("notif");

                    BildirimAlani.find(".notif").removeClass("hide");

                    return CallBack(Settings.callBy);

                }, 700);

            });

        }, 1500);

    }

}

function App(p) {
    return new Application(p);
}