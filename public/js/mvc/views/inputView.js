window.InputView = Backbone.View.extend({
    
    model:Input,
    
    initialize: function(){
        
        this.initAutocomplete();
        
        this.collection.on('add',function(tag){
            this.addTag(tag);
        },this);
        
        this.initPopulate();
        
        
    },
    
    initPopulate: function(){
        if (this.model.get('populate')){
            var self = this;
            $.each(this.model.get('populate'),function(index,value){

                self.createTagAndHiddenTagSimple(value,index,false);
            });
        }
    },
    
    initAutocomplete: function(){
        
        if (this.model.get('autocomplete')){
            return;
        };
        
        var self = this;
        
        this.$el.autocomplete({
                target: $('#suggestions_item_'+this.model.get('itemName')),
                source: this.model.get('autocomplete').data,
                callback: function(e) {
                        var value = $(e.currentTarget).text();
                        self.createTagAndHiddenTagSimple(
                            value,
                            $.parseJSON($(e.currentTarget).attr('data-autocomplete')).value,
                            false
                        );
                    
                       //removing value from autocomplete
                        self.model.get('autocomplete').data.splice(
                            $.inArray(value, self.model.get('autocomplete').data)
                            ,1
                        );
                        self.$el.autocomplete("update",{
                            source: self.model.get('autocomplete').data
                        });
                        self.$el.autocomplete('clear');
                },
                link: 'target.html?term=',
                minLength: 1
        });
    },
    
    events:{
        'keypress':'enterTagContent'
    },
    
    
    
    createTagAndHiddenTagSimple: function(text,valueForInput,isNewTag){
        var tag = this.collection.createTag(text,valueForInput);
        
        //if only one element is allowed, we replace it if already choosen
        if ((this.collection.length > 0) && (this.model.get('multitag') == false)){
            
            this.collection.remove(this.collection.get(0));
        }
        
        this.collection.add(tag);
        
        new HiddenTagSimpleFormView({
            model:tag,
            'isNewTag': isNewTag,
            'formElementName' : this.model.get('formElementName'),
            'itemId' : this.model.get('itemId'),
            'tagsContainer' : this.model.get('tagsContainer')
        });
    },
    
    enterTagContent: function(e){
        
        var value = $(e.currentTarget).val();
        
        //if enter is pressed and tag value is valid
        if (e.keyCode == 13 && (value) && (this.collection.validateTag(value))) {
            
            var id = this.model.getIdTag(value);
            var $subform = this.model.get('itemGroupForm');
            if(id){
                this.createTagAndHiddenTagSimple(value,id,false);
                
            } else if ($subform.length > 0){
                
                var subform = new SubformView ({
                        el : $subform,
                        model: this.model,
                        tag : this.collection.createTag(value,'',true),
                        collection : this.collection
                });
                
                //go to the subform page
                $.mobile.changePage('#'+subform.$el.attr('id'));
                
            //otherwse if no subform is needed create tag
            } else {
                
                this.createTagAndHiddenTagSimple(value,'',true);
            }
            
            return false;
        }
    },
    
    
    
    addTag:function(tag){
        
        var tagView = new TagView({model:tag,collection:this.collection});
        
        //add new tag to be seen
        $('#'+this.model.get('propertyName')+'_page').find('div[data-role=content]').first().append(tagView.render()).trigger('create');
        
        //remove placeholder from input
        this.$el.val('').removeAttr('placeholder'); 
        this.$el.focus();
        
        return false;
    }
});

