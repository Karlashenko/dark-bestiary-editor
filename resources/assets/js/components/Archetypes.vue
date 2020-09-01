<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="archetypes" :fields="['id', 'name']" v-model="selected"></list>
                </div>

                <div class="col-md-9">
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
                                    <td>Name</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.name">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <table style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Min</th>
                                                <th>Max</th>
                                                <th>x33</th>
                                                <th>x55</th>
                                                <th>Curve</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(attribute) in selected.attributes">
                                                <td style="width: 40%;">
                                                    <selectpicker v-model="attribute.id"
                                                                  :nullable="false"
                                                                  :options="attributes"
                                                                  :valuecallback="(attribute) => attribute.id"
                                                                  :labelcallback="(attribute) => attribute.type">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" v-model="attribute.pivot.min">
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" v-model="attribute.pivot.max">
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" :value="attribute.pivot.min * 33">
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" :value="attribute.pivot.min * 55">
                                                </td>

                                                <td style="width: 20%;">
                                                    <div class="form-group">
                                                        <selectpicker v-model="attribute.pivot.curve_type" :nullable="false" :options="window.library.curveTypes"></selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeAttribute(selected, attribute)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;" v-if="selected.attributes.length < attributes.length">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="resetAttributes(selected)">
                                                        <span class="glyphicon glyphicon-import"></span>
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
                                    <td colspan="2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="text-center">
                                                    <input type="number" placeholder="Level" class="form-control" v-model="level"/>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <table class="table">
                                                            <tr v-for="attribute in selected.attributes">
                                                                <td class="text-left"><samp>{{ attribute.name_i18n.en }}</samp></td>
                                                                <td class="text-right"><samp><b>{{ window.evaluateCurve(level, attribute.pivot.min, attribute.pivot.max, attribute.pivot.curve_type).toFixed(2) }}</b></samp></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2"><hr></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-left"><samp>Melee</samp></td>
                                                                <td class="text-right"><samp><b>{{ (getPropertyBonus(selected, selected.properties.find(p => p.type === 'MeleeAttackPower')) * 4 / 2.2).toFixed(2) }}</b></samp></td>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-left"><samp>Ranged</samp></td>
                                                                <td class="text-right"><samp><b>{{ (getPropertyBonus(selected, selected.properties.find(p => p.type === 'RangedAttackPower')) * 4 / 2.2).toFixed(2) }}</b></samp></td>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-left"><samp>Spell</samp></td>
                                                                <td class="text-right"><samp><b>{{ (getPropertyBonus(selected, selected.properties.find(p => p.type === 'SpellPower')) * 4 / 2.2).toFixed(2) }}</b></samp></td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                    <div class="col-md-8">
                                                        <div class="row" style="border-spacing: 0">
                                                            <div class="col-md-6">
                                                                <table class="table">
                                                                    <tr v-for="property in selected.properties.slice(0, 22)">
                                                                        <td class="text-left"><samp>{{ property.name_i18n.en }}</samp></td>
                                                                        <td class="text-right bold">
                                                                            <samp>
                                                                                <b>{{ getPropertyBonus(selected, property).toFixed(2) }}</b>
                                                                            </samp>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <table class="table">
                                                                    <tr v-for="property in selected.properties.slice(22)">
                                                                        <td class="text-left"><samp>{{ property.name_i18n.en }}</samp></td>
                                                                        <td class="text-right bold">
                                                                            <samp>
                                                                                <b>{{ getPropertyBonus(selected, property).toFixed(2) }}</b>
                                                                            </samp>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-md-2" v-for="(attribute, index) in selected.attributes">
                                                <attribute-chart :attribute="attribute"></attribute-chart>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <table style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Min</th>
                                                <th>Max</th>
                                                <th>x55</th>
                                                <th>Curve</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <tr v-for="property in selected.properties">
                                                <td style="width: 40%;">
                                                    <selectpicker v-model="property.id"
                                                                  :nullable="false"
                                                                  :options="properties"
                                                                  :valuecallback="(property) => property.id"
                                                                  :labelcallback="(property) => property.type">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" v-model="property.pivot.min">
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" v-model="property.pivot.max">
                                                </td>

                                                <td style="width: 10%;">
                                                    <input type="number" class="form-control" :value="property.pivot.min * 55">
                                                </td>

                                                <td style="width: 20%;">
                                                    <div class="form-group">
                                                        <selectpicker v-model="property.pivot.curve_type" :nullable="false" :options="window.library.curveTypes"></selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeProperty(selected, property)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;" v-if="selected.properties.length < properties.length">
                                                <td colspan="4">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="resetProperties(selected)">
                                                        <span class="glyphicon glyphicon-import"></span>
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
    import {Events} from "./../events";

    export default {
        data() {
            return {
                archetypes: [],
                window: window,
                attributes: [],
                properties: [],
                level: [],
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
            getPropertyBonus: function (archetype, property) {
                if (property === undefined) {
                    return 0;
                }

                let bonus = 0;

                for (let attributeIndex in archetype.attributes) {
                    for (let propertyIndex in archetype.attributes[attributeIndex].properties) {
                        if (archetype.attributes[attributeIndex].properties[propertyIndex].id == property.id) {
                            bonus += window.evaluateCurve(
                                this.level,
                                archetype.attributes[attributeIndex].pivot.min,
                                archetype.attributes[attributeIndex].pivot.max,
                                archetype.attributes[attributeIndex].pivot.curve_type)
                                * parseFloat(archetype.attributes[attributeIndex].properties[propertyIndex].pivot.value);
                        }
                    }
                }

                return parseFloat(property.pivot.min) + bonus;
            },

            removeProperty: function (archetype, property) {
                let index = archetype.properties.indexOf(property);
                archetype.properties.splice(index, 1);
            },

            resetProperties: function (archetype) {
                for (const key in this.properties) {
                    let property = archetype.properties.find((element) => {
                        return element.id === this.properties[key].id;
                    });

                    if (property) {
                        continue;
                    }

                    property = Object.assign({}, this.properties[key]);
                    property.pivot = {value: 0, curve_type: "Linear"};

                    archetype.properties.push(property);
                }
            },

            resetAttributes: function (archetype) {
                for (const key in this.attributes) {
                    let attribute = archetype.attributes.find((element) => {
                        return element.id === this.attributes[key].id;
                    });

                    if (attribute) {
                        continue;
                    }

                    attribute = Object.assign({}, this.attributes[key]);
                    attribute.pivot = {value: 0, curve_type: "Linear"};

                    archetype.attributes.push(attribute);
                }
            },

            removeAttribute: function (archetype, attribute) {
                let index = archetype.attributes.indexOf(attribute);
                archetype.attributes.splice(index, 1);
            },

            updateAttributes: function () {
                let total = 0;

                for (let key in this.selected.attributes) {
                    total += parseInt(this.selected.attributes[key].pivot.value);
                }

                $("#total").text(this.maxAttributes - total);
            },

            load: function () {
                window.axios.get("/frontend/archetypes").then(response => {
                    this.archetypes = response.data;
                    this.select(this.id === undefined ? this.archetypes[0] : this.archetypes.find((element) => element.id === this.id));
                });

                window.axios.get("/frontend/attributes").then(response => {
                    this.attributes = response.data;
                });

                window.axios.get("/frontend/properties").then(response => {
                    this.properties = response.data;
                });
            },

            create: function () {
                this.archetypes.push({
                    fresh: true,
                    name: "Just Another Archetype",
                    attributes: [],
                    properties: []
                });

                this.select(this.archetypes[this.archetypes.length - 1]);
            },

            select: function (archetype) {
                this.selected = archetype;
            },

            duplicate: function (archetype) {
                let copy = Object.assign({}, archetype);
                copy.fresh = true;
                this.archetypes.push(copy);
                this.select(copy);
            },

            reset: function (archetype) {
                if (archetype.fresh) {
                    return;
                }

                let index = this.archetypes.indexOf(archetype);

                window.axios.get("/frontend/archetypes/" + archetype.id).then(response => {
                    this.archetypes[index] = response.data;
                    this.select(this.archetypes[index]);
                });
            },

            save: function (archetype) {
                let index = this.archetypes.indexOf(archetype);

                if (archetype.fresh) {
                    window.axios.post("/frontend/archetypes", archetype).then(response => {
                        this.archetypes[index] = response.data;
                        this.select(this.archetypes[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/archetypes/" + archetype.id, archetype).then(response => {
                    this.archetypes[index] = response.data;
                    this.select(this.archetypes[index]);
                });
            },

            destroy: function (archetype) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.archetypes.indexOf(archetype);

                if (archetype.fresh) {
                    this.archetypes.splice(index, 1);
                    this.select(this.archetypes[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/archetypes/" + archetype.id).then(() => {
                    this.archetypes.splice(index, 1);
                    this.select(this.archetypes[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.archetypes) {
                    let archetype = this.archetypes[key];

                    if (archetype.fresh) {
                        window.axios.post("/frontend/archetypes", archetype).then(response => {
                            this.archetypes[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/archetypes/" + archetype.id, archetype).then(response => {
                        this.archetypes[key] = response.data;
                    });
                }
            }
        }
    };
</script>
