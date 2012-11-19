window.InputView = Backbone.View.extend({
    model:Input,
    collection: new Tags(),
    initialize: function(){
        
        if (this.model.get('autocomplete')){
            this.initAutocomplete();
        };
            
        this.collection.on('addTag:'+this.model.get('propertyName'),function(tag){
            this.addTag(tag);
        },this);
        
        this.collection.on('remove',function(tag){
            console.log('todo remove hiddenform');
        },this);
        
        //case tags with subform
        var $subforms = $('#'+this.model.get('propertyName')+'_itemGroup_form_page');
        if ($subforms.length > 0){
            this.options.subform = new SubformView ({
                    el : $subforms,
                    model : this.model,
                    collection : this.collection
            });
        } 
    },
    
    initAutocomplete: function(){
        var self = this;
        
        this.$el.autocomplete({
                target: $('#suggestions_item_'+this.model.get('itemName')),
                source: this.model.get('autocomplete').data,
                callback: function(e) {
                        var value = $(e.currentTarget).text();
                    
                        //trigger add tag
                        var tag = new Tag({
                            text: value,
                            id: self.collection.length,
                            valueForInput : $.parseJSON($(e.currentTarget).attr('data-autocomplete')).value
                        });
                        
                        self.collection.addTag(tag,'addTag:'+self.model.get('propertyName')); 
                        self.addSingleInputElement({model:tag,'isNewTag': false});
                        
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
        'keypress':'openSubForm'
    },
    
    openSubForm: function(e){
        
        var value = $(e.currentTarget).val();
        
        //if enter is pressed
        if (e.keyCode == 13 && (value)){
            
            //if tag element with subform and new tag
            if (!this.model.isAlreadyTag(value) && this.options.subform){
                
                this.model.set('inputTag',value);
                
                //go to the subform page
                $.mobile.changePage('#'+this.options.subform.$el.attr('id'));                
            } else if(!this.model.isAlreadyTag(value)){
                
                var tag = new Tag({
                    text:value,
                    id:this.collection.length
                });
                
                this.addSingleInputElement({model:tag,'isNewTag': true});
                
                //add tag to collection and trigger event add
                this.collection.addTag(
                    tag,
                    'addTag:'+this.model.get('propertyName')
                );
            }
            
            return false;
        }
    },
    
    addSingleInputElement: function(options){
        
        //add hidden input with name in order to get through post : 
        //format : array(property => array(array(new => value,value....)
        //new => value for new tags
        options['formElementName'] = this.model.get('formElementName');
        options['itemId'] = this.model.get('itemId');
        
        var hiddenForm = new HiddenTagSimpleFormView(options);
        this.model.get('tagsContainer').append(hiddenForm.render()).after(hiddenForm.options.hiddenElement);
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

