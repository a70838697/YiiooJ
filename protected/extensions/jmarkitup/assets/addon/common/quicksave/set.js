// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {    
    markupSet:  [
        {name:'Save', className:'save', beforeInsert:function(markItUp) { miu.save(markItUp) } },
        {name:'Load', className:'load', beforeInsert:function(markItUp) { miu.load(markItUp) } }
    ]
}
    
// mIu nameSpace to avoid conflict.
miu = {
    save: function(markItUp) {
        var data = encodeURIComponent(markItUp.textarea.value); // Thx Gregory LeRoy
        var ok = confirm("Save the content?");
        if (!ok) {
            return false;
        }
        $.post(markItUp.root+"utils/quicksave/save.php", "data="+data, function(response) {
                if(response === "MIU:OK") {
                    alert("Saved!");
                }
            }
        ); 
    },
	
    load: function(markItUp) {
        $.get(markItUp.root+"utils/quicksave/load.php", function(response) {
                if(response === "MIU:EMPTY") {
                    alert("Nothing to load");
                } else {
                    var ok = confirm("Load the previously saved content?");
                    if (!ok) {
                        return false;
                    }
                    markItUp.textarea.value = response;
                    alert("Loaded!");
                }
            }
        );
    }      
}
