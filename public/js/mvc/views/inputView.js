window.InputView = Backbone.View.extend({
    model:Input,
    initialize: function(){
        
        if (this.model.get('autocomplete')){
            this.initAutocomplete();
        };
        
        this.collection.on('addTag:'+this.model.get('propertyName'),function(tag){
            this.addTag(tag);
        },this);
        
        this.collection.on('remove',function(tag){
            console.log('input[name='
                +this.model.get('propertyName')
                +'['+tag.get('id')+'][id]');
            this.model.get('tagsContainer').find('input[name="'
                +this.model.get('propertyName')
                +'['+tag.get('id')+'][id]"]').remove();
        },this);
        
        if (this.model.get('populate')){
            this.initPopulate();
        };
        
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
    
    initPopulate: function(){
        var self = this;
        $.each(this.model.get('populate'),function(index,value){
            
            self.createTag(value,index,false);
        });
    },
    
    createTag: function(text,valueForInput,isNewTag){
        
        if ($.inArray(text,this.collection.pluck("text")) > -1){
            return;
        }
        
        var tag = new Tag();
        tag.set({
            text:text,
            id: this.collection.length,
            valueForInput:valueForInput
        });
        this.collection.addTag(tag,'addTag:'+this.model.get('propertyName'));
        this.addSingleInputElement({model:tag,'isNewTag': isNewTag});
        
        return tag;
    },
    
    initAutocomplete: function(){
        var self = this;
        
        this.$el.autocomplete({
                target: $('#suggestions_item_'+this.model.get('itemName')),
                source: this.model.get('autocomplete').data,
                callback: function(e) {
                        var value = $(e.currentTarget).text();
                        self.createTag(
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
        'keypress':'validateTagContent'
    },
    
    validateTagContent: function(e){
        
        var value = $(e.currentTarget).val();
        
        //if enter is pressed
        if (e.keyCode == 13 && (value)){
            
            //if tag element with subform and new tag
            if (!this.model.isAlreadyTag(value) && this.options.subform){
                
                this.model.set('inputTag',value);
                
                //go to the subform page
                $.mobile.changePage('#'+this.options.subform.$el.attr('id'));

            //otherwse if no subform is needed create tag
            } else if (!this.model.isAlreadyTag(value)){
                
                var isNewTag = true;
                var valueForInput;
                $.each(this.model.get('autocomplete').data,function(key,value){
                    if (value.label == value){
                        isNewTag = false;
                        valueForInput = value.value;
                    }
                });
                
                this.createTag(value,valueForInput,isNewTag);
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

