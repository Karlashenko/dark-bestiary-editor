<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-5">
                    <list :elements="itemModifiers" :fields="['id', 'suffix_i18n.en|label', 'inline_stats']" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-7">
                    <div class="panel panel-default" v-if="selected">
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
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Suffix I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.suffix_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Base</td>
                                    <td>
                                        <input type="checkbox" v-model="selected.is_base">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Suffix</td>
                                    <td>
                                        <input type="checkbox" v-model="selected.is_suffix">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Weapon</td>
                                    <td>
                                        <input type="checkbox" v-model="selected.is_weapon">
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="white-space: nowrap;">Modifiers</td>
                                    <td style="width: 100%">
                                        <table style="width: 100%; table-layout: auto;">
                                            <tr v-for="itemModifier in selected.item_modifiers">
                                                <td style="width: 100%;">
                                                    <selectpicker v-model="itemModifier.id"
                                                                  :nullable="true"
                                                                  :options="itemModifiers"
                                                                  :valuecallback="(element) => element.id"
                                                                  :labelcallback="(element) => window.getPropertyByPath(element, 'suffix_i18n.en|label')">
                                                    </selectpicker>
                                                </td>

                                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                                    <button class="btn btn-default" @click="itemModifierMoveUp(selected, itemModifier)">
                                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="itemModifierMoveDown(selected, itemModifier)">
                                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="removeItemModifier(selected, itemModifier)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addItemModifier(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Attributes</td>
                                    <td>
                                        <table style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Min</th>
                                                <th>Max</th>
                                                <th>Fraction</th>
                                                <th>Curve</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(attribute) in selected.attributes">
                                                <td style="width: 30%;">
                                                    <selectpicker v-model="attribute.id"
                                                                  :nullable="false"
                                                                  :options="attributes"
                                                                  :valuecallback="(attribute) => attribute.id"
                                                                  :labelcallback="(attribute) => attribute.type">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 15%;">
                                                    <input type="number" class="form-control" v-model="attribute.pivot.min">
                                                </td>

                                                <td style="width: 15%;">
                                                    <input type="number" class="form-control" v-model="attribute.pivot.max">
                                                </td>

                                                <td style="width: 15%;">
                                                    <input type="number" class="form-control" v-model="attribute.pivot.fraction">
                                                </td>

                                                <td style="width: 15%;">
                                                    <div class="form-group">
                                                        <selectpicker v-model="attribute.pivot.curve_type" :nullable="false" :options="window.library.curveTypes"></selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeAttribute(selected, attribute)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAttribute(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Properties</td>
                                    <td>
                                        <table style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Min</th>
                                                <th>Max</th>
                                                <th>Fraction</th>
                                                <th>Curve</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <tr v-for="property in selected.properties">
                                                <td style="width: 30%;">
                                                    <selectpicker v-model="property.id"
                                                                  :nullable="false"
                                                                  :options="properties"
                                                                  :valuecallback="(property) => property.id"
                                                                  :labelcallback="(property) => property.type">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 15%;">
                                                    <input type="number" class="form-control" v-model="property.pivot.min">
                                                </td>

                                                <td style="width: 15%;">
                                                    <input type="number" class="form-control" v-model="property.pivot.max">
                                                </td>

                                                <td style="width: 15%;">
                                                    <input type="number" class="form-control" v-model="property.pivot.fraction">
                                                </td>

                                                <td style="width: 15%;">
                                                    <div class="form-group">
                                                        <selectpicker v-model="property.pivot.curve_type" :nullable="false" :options="window.library.curveTypes"></selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeProperty(selected, property)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="4">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addProperty(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Categories</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="category in selected.categories">
                                                <td>
                                                    <div class="form-group">
                                                        <selectpicker v-model="category.id"
                                                                      :nullable="false"
                                                                      :options="categories"
                                                                      :valuecallback="(element) => element.id"
                                                                      :labelcallback="(element) => element.type">
                                                        </selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeCategory(selected, category)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addCategory(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <input type="number" placeholder="Level" class="form-control" v-model="level"/>
                                                </div>

                                                <div class="row" style="padding: 0 25px;">
                                                    <table class="table">
                                                        <tr v-for="attribute in selected.attributes">
                                                            <td class="text-left"><samp>{{ attribute.name_i18n.en }}</samp></td>
                                                            <td class="text-right"><samp><b>{{ window.evaluateCurve(level, attribute.pivot.min, attribute.pivot.max, attribute.pivot.curve_type).toFixed(2) }}</b></samp></td>
                                                        </tr>
                                                    </table>

                                                    <table class="table">
                                                        <tr v-for="property in selected.properties">
                                                            <td class="text-left"><samp>{{ property.name_i18n.en }}</samp></td>
                                                            <td class="text-right bold">
                                                                <samp>
                                                                    <b>{{ window.evaluateCurve(level, property.pivot.min, property.pivot.max, property.pivot.curve_type).toFixed(2) }}</b>
                                                                </samp>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
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
    import {Events} from "./../events";

    export default {
        data() {
            return {
                itemModifiers: [],
                itemRarities: [],
                level: 0,
                loading: true,
                window: window,
                attributes: [],
                properties: [],
                categories: [],
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);

            this.load();
        },

        methods: {
            load: function () {
                window.axios.get("/frontend/item_modifiers").then(response => {
                    this.itemModifiers = response.data;
                    this.select(this.id === undefined ? this.itemModifiers[0] : this.itemModifiers.find((element) => element.id === this.id));
                }).then(() => {
                    window.axios.get("/frontend/item_rarities").then(response => {
                        this.itemRarities = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/attributes").then(response => {
                        this.attributes = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/properties").then(response => {
                        this.properties = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/item_categories").then(response => {
                        this.categories = response.data;
                        this.loading = false;
                    });
                });
            },

            getInlineStats: function (modifier) {
                return '<span style="font-family: monospace;">' + modifier.item_modifiers.map(a => window.getPropertyByPath(a, 'label|suffix_i18n.en').replace('Attribute - ', '').replace('Property - ').replace('undefined', '')).join(', ') + '</span>';
            },

            getFilters: function () {
                return [
                    {
                        title: "Rarity",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.itemRarities,
                            valuecallback: (rarity) => rarity.id,
                            labelcallback: (rarity) => rarity.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.rarity_id === value
                        }
                    },
                    {
                        title: "Is Suffix",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_suffix == value;
                        },
                        value: null
                    },
                    {
                        title: "Is Weapon",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_weapon == value;
                        },
                        value: null
                    },
                    {
                        title: "Is Base",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_base == value;
                        },
                        value: null
                    }
                ];
            },

            arrayContainsArray: function (superset, subset) {
                return subset.every(function (value) {
                    return (superset.indexOf(value) >= 0);
                });
            },

            arrayContainsAny: function (superset, subset) {
                return subset.every(function (value) {
                    return (superset.indexOf(value) >= 0);
                });
            },

            addCategory: function (modifier) {
                modifier.categories.push({
                    id: this.categories[0] ? this.categories[0].id : undefined
                });
            },

            removeCategory: function (modifier, category) {
                let index = modifier.categories.indexOf(category);
                modifier.categories.splice(index, 1);
            },

            removeProperty: function (itemModifier, property) {
                let index = itemModifier.properties.indexOf(property);
                itemModifier.properties.splice(index, 1);
            },

            addProperty: function (itemModifier) {
                itemModifier.properties.push({
                    id: this.properties[0] ? this.properties[0].id : undefined,
                    name_i18n: this.properties[0] ? this.properties[0].name_i18n : undefined,
                    pivot: {
                        min: 0,
                        max: 0,
                        curve_type: "Linear"
                    }
                });
            },

            addAttribute: function (itemModifier) {
                itemModifier.attributes.push({
                    id: this.attributes[0] ? this.attributes[0].id : undefined,
                    name_i18n: this.attributes[0] ? this.attributes[0].name_i18n : undefined,
                    pivot: {
                        min: 0,
                        max: 0,
                        curve_type: "Linear"
                    }
                });
            },

            removeAttribute: function (itemModifier, attribute) {
                let index = itemModifier.attributes.indexOf(attribute);
                itemModifier.attributes.splice(index, 1);
            },

            create: function () {
                this.itemModifiers.push({
                    fresh: true,
                    label: "",
                    attributes: [],
                    properties: []
                });

                this.select(this.itemModifiers[this.itemModifiers.length - 1]);
            },

            select: function (itemModifier) {
                this.selected = itemModifier;
            },

            duplicate: function (itemModifier) {
                let copy = Object.assign({}, itemModifier);
                copy.fresh = true;
                this.itemModifiers.push(copy);
                this.select(copy);
            },

            reset: function (itemModifier) {
                if (itemModifier.fresh) {
                    return;
                }

                let index = this.itemModifiers.indexOf(itemModifier);

                window.axios.get("/frontend/item_modifiers/" + itemModifier.id).then(response => {
                    this.itemModifiers[index] = response.data;
                    this.select(this.itemModifiers[index]);
                });
            },

            addItemModifier: function (itemModifier) {
                if (itemModifier.item_modifiers === undefined) {
                    itemModifier.item_modifiers = [];
                }

                itemModifier.item_modifiers.push({
                    id: this.itemModifiers[0] ? this.itemModifiers[0].id : undefined
                });
            },

            removeItemModifier: function (itemModifier, element) {
                let index = itemModifier.item_modifiers.indexOf(element);
                itemModifier.item_modifiers.splice(index, 1);
            },

            itemModifierMoveUp: function (itemModifier, element) {
                let index = itemModifier.item_modifiers.indexOf(element);

                if (index === -1 || index === 0) {
                    return;
                }

                itemModifier.item_modifiers[index] = itemModifier.item_modifiers[index - 1];
                itemModifier.item_modifiers[index - 1] = element;

                this.$forceUpdate();
            },

            itemModifierMoveDown: function (itemModifier, element) {
                let index = itemModifier.item_modifiers.indexOf(element);

                if (index === -1 || index === itemModifier.skills.length) {
                    return;
                }

                itemModifier.item_modifiers[index] = itemModifier.item_modifiers[index + 1];
                itemModifier.item_modifiers[index + 1] = element;

                this.$forceUpdate();
            },

            save: function (itemModifier) {
                let index = this.itemModifiers.indexOf(itemModifier);

                if (itemModifier.fresh) {
                    window.axios.post("/frontend/item_modifiers", itemModifier).then(response => {
                        this.itemModifiers[index] = response.data;
                        this.select(this.itemModifiers[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/item_modifiers/" + itemModifier.id, itemModifier).then(response => {
                    this.itemModifiers[index] = response.data;
                    this.select(this.itemModifiers[index]);
                });
            },

            destroy: function (itemModifier) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.itemModifiers.indexOf(itemModifier);

                if (itemModifier.fresh) {
                    this.itemModifiers.splice(index, 1);
                    this.select(this.itemModifiers[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/item_modifiers/" + itemModifier.id).then(() => {
                    this.itemModifiers.splice(index, 1);
                    this.select(this.itemModifiers[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.itemModifiers) {
                    let itemModifier = this.itemModifiers[key];

                    if (itemModifier.fresh) {
                        window.axios.post("/frontend/item_modifiers", itemModifier).then(response => {
                            this.itemModifiers[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/item_modifiers/" + itemModifier.id, itemModifier).then(response => {
                        this.itemModifiers[key] = response.data;
                    });
                }
            }
        }
    };
</script>
