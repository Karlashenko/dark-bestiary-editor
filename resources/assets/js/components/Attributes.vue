<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="attributes" :fields="['id', 'index', 'name_i18n.en', 'type']" :icon="'icon'" v-model="selected"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Primary</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_primary"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Secondary</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_secondary"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Index</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.index">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.type">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr v-if="selected.property_modifiers">
                                    <td>Property Modifiers</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th style="width: 15%">Property</th>
                                                <th style="width: 10%">Amount</th>
                                                <th style="width: 15%">Modifier</th>
                                                <th style="width: 10%">Fraction</th>
                                                <th style="width: 15%">Property</th>
                                                <th style="width: 15%">Attribute</th>
                                                <th style="width: 20%">Formula</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="modifier in selected.property_modifiers">
                                                <td>
                                                    <selectpicker v-model="modifier.property_id"
                                                                  :nullable="true"
                                                                  :options="properties"
                                                                  :valuecallback="(property) => property.id"
                                                                  :labelcallback="(property) => property.name_i18n.en">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="modifier.amount">
                                                </td>

                                                <td>
                                                    <selectpicker v-model="modifier.type" :options="window.library.modifierTypes"></selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="modifier.fraction">
                                                </td>

                                                <td>
                                                    <selectpicker v-model="modifier.fraction_property_id"
                                                                  :nullable="true"
                                                                  :options="properties"
                                                                  :valuecallback="(property) => property.id"
                                                                  :labelcallback="(property) => property.name_i18n.en">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <selectpicker v-model="modifier.fraction_attribute_id"
                                                                  :nullable="true"
                                                                  :options="attributes"
                                                                  :valuecallback="(attribute) => attribute.id"
                                                                  :labelcallback="(attribute) => attribute.name_i18n.en">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <input type="text" class="form-control" v-model="modifier.formula">
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removePropertyModifier(selected, modifier)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="3">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addPropertyModifier(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="panel-footer">
                            <div class="pull-left">
                                <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                                <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Events} from './../events';

    export default {
        data() {
            return {
                attributes: [],
                properties: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);

            this.load();
        },

        methods: {
            addPropertyModifier: function (item) {
                item.property_modifiers.push({
                    property_id: this.properties[0] ? this.properties[0].id : undefined,
                    type: window.library.modifierTypes[0],
                    value: 0
                });
            },

            removePropertyModifier: function (item, modifier) {
                let index = item.property_modifiers.indexOf(modifier);
                item.property_modifiers.splice(index, 1);
            },

            load: function () {
                window.axios.get('/frontend/attributes').then(response => {
                    this.attributes = response.data;
                    this.select(this.id === undefined ? this.attributes[0] : this.attributes.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/properties').then(response => {
                    this.properties = response.data;
                });
            },

            create: function () {
                this.attributes.push({
                    fresh: true,
                    name_i18n: {en: "attribute_"}
                });

                this.select(this.attributes[this.attributes.length - 1]);
            },

            select: function (attribute) {
                this.selected = attribute;
            },

            duplicate: function (attribute) {
                let copy = Object.assign({}, attribute);
                copy.fresh = true;
                this.attributes.push(copy);
                this.select(copy);
            },

            reset: function (attribute) {
                if (attribute.fresh) {
                    return;
                }

                let index = this.attributes.indexOf(attribute);

                window.axios.get('/frontend/attributes/' + attribute.id).then(response => {
                    this.attributes[index] = response.data;
                    this.select(this.attributes[index]);
                });
            },

            save: function (attribute) {
                let index = this.attributes.indexOf(attribute);

                if (attribute.fresh) {
                    window.axios.post('/frontend/attributes', attribute).then(response => {
                        this.attributes[index] = response.data;
                        this.select(this.attributes[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/attributes/' + attribute.id, attribute).then(response => {
                    this.attributes[index] = response.data;
                    this.select(this.attributes[index]);
                });
            },

            destroy: function (attribute) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.attributes.indexOf(attribute);

                if (attribute.fresh) {
                    this.attributes.splice(index, 1);
                    this.select(this.attributes[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/attributes/' + attribute.id).then(() => {
                    this.attributes.splice(index, 1);
                    this.select(this.attributes[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.attributes) {
                    let attribute = this.attributes[key];

                    if (attribute.fresh) {
                        window.axios.post('/frontend/attributes', attribute).then(response => {
                            this.attributes[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/attributes/' + attribute.id, attribute).then(response => {
                        this.attributes[key] = response.data;
                    });
                }
            }
        }
    };
</script>
