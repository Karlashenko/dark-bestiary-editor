<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-4">
                    <list :elements="skills" :fields="['id', 'name_i18n.en|label', 'effect.name', 'type', 'category.name_i18n.en', 'rarity.type', 'prefix']" :icon="'icon'" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-8">
                    <ul class="nav nav-tabs">
                        <li role="presentation" @click="tabIndex = 0" :class="tabIndex === 0 ? 'active' : ''"><a href="javascript:void(0)">General</a></li>
                        <li role="presentation" @click="tabIndex = 1" :class="tabIndex === 1 ? 'active' : ''"><a href="javascript:void(0)">Effects</a></li>
                    </ul>

                    <div class="panel panel-default panel-tabs" v-if="selected">
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
                            <table class="form-table" v-if="tabIndex === 0">
                                <tr>
                                    <td style="white-space: nowrap;">Skills</td>
                                    <td style="width: 100%">
                                        <table style="width: 100%; table-layout: auto;">
                                            <tr v-for="skill in selected.skills">
                                                <td style="width: 100%;">
                                                    <skill-field v-model="skill.id" :skills="skills"></skill-field>
                                                </td>

                                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                                    <button class="btn btn-default" @click="skillMoveUp(selected, skill)">
                                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="skillMoveDown(selected, skill)">
                                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="removeSkill(selected, skill)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addSkill(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Resources Costs</td>
                                    <td>
                                        <table class="form-table">
                                            <tbody>
                                            <tr v-for="cost in selected.costs">
                                                <td>
                                                    <selectpicker v-model="cost.resource_type" :options="window.library.resourceTypes"></selectpicker>
                                                </td>

                                                <td style="text-align: center;">
                                                    <input type="number" class="form-control" v-model="cost.amount">
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeCost(selected, cost)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addCost(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Category</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.category_id"
                                                          :nullable="true"
                                                          :options="categories"
                                                          :valuecallback="(category) => category.id"
                                                          :labelcallback="(category) => category.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Enabled</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_enabled"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Commentary</td>
                                    <td>
                                        <textarea style="min-height: 100px;" class="form-control" v-model="selected.commentary"></textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Prefix</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.prefix"></input>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Label</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.label"></input>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled/>
                                        </div>
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

                                <tr>
                                    <td>Lore I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.lore_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.type" :options="window.library.skillTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Rarity</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.rarity_id"
                                                          :nullable="true"
                                                          :options="itemRarities"
                                                          :valuecallback="(itemRarity) => itemRarity.id"
                                                          :labelcallback="(itemRarity) => itemRarity.type">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Required Level</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.required_level">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Required Item Category</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.required_item_category_id"
                                                          :nullable="true"
                                                          :options="itemCategories"
                                                          :valuecallback="(itemCategory) => itemCategory.id"
                                                          :labelcallback="(itemCategory) => itemCategory.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Target</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.target_type" :options="window.library.skillTargetTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Animation</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.animation" :nullable="true" :options="window.library.animations"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Cooldown</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.cooldown">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>AOE</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.aoe">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>AOE Shape</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.aoe_shape" :options="window.library.shapes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Range Min</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.range_min">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Range Max</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.range_max">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Range Shape</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.range_shape" :options="window.library.shapes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Flags</td>
                                    <td>
                                        <flags v-model="selected.flags" :flags="window.library.skillFlags"></flags>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Price</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <tr v-for="price in selected.prices">
                                                <td style="width: 50%;">
                                                    <selectpicker v-model="price.currency_id"
                                                                  :options="currencies"
                                                                  :valuecallback="(currency) => currency.id"
                                                                  :labelcallback="(currency) => currency.name_i18n.en">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="price.amount">
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removePrice(selected, price)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="3">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addPrice(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table class="form-table" v-if="tabIndex === 1">
                                <tr>
                                    <td colspan="2">
                                        <effect-field v-model="selected.effect_id" :effects="effects"></effect-field>
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
                tabIndex: 0,
                loading: false,
                categories: [],
                skills: [],
                effects: [],
                currencies: [],
                behaviours: [],
                itemCategories: [],
                itemRarities: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            this.load();

            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);
        },

        methods: {
            load: function () {
                this.loading = true;

                window.axios.get("/frontend/skills").then(response => {
                    this.skills = response.data;
                    this.select(this.id === undefined ? this.skills[0] : this.skills.find((element) => element.id === this.id));
                }).then(() => {
                    window.axios.get("/frontend/effects").then(response => {
                        this.effects = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/currencies').then(response => {
                        this.currencies = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/item_categories").then(response => {
                        this.itemCategories = response.data;
                    });
                })
                .then(() => {
                    window.axios.get("/frontend/item_rarities").then(response => {
                        this.itemRarities = response.data;
                    });
                })
                .then(() => {
                    window.axios.get("/frontend/behaviours").then(response => {
                        this.behaviours = response.data;
                    });
                })
                .then(() => {
                    window.axios.get("/frontend/skill_categories").then(response => {
                        this.categories = response.data;
                        this.loading = false;
                    });
                });
            },

            getFilters: function () {
                return [
                    {
                        title: "Type",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: window.library.skillTypes
                        },
                        filter: (element, value) => {
                            return !value || element.type === value
                        },
                        value: null
                    },
                    {
                        title: "Target",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: window.library.skillTargetTypes
                        },
                        filter: (element, value) => {
                            return !value || element.target_type === value
                        },
                        value: null
                    },
                    {
                        title: "AOE Shape",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: window.library.shapes
                        },
                        filter: (element, value) => {
                            return !value || element.aoe_shape === value
                        },
                        value: null
                    },
                    {
                        title: "Category",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.categories,
                            valuecallback: (category) => category.id,
                            labelcallback: (category) => category.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.category_id === value
                        },
                        value: null
                    },
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
                        },
                        value: null
                    },
                    {
                        title: "Enabled",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_enabled == value;
                        },
                        value: true
                    },
                    {
                        title: "Flags",
                        component: "flags",
                        props: {
                            flags: window.library.skillFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                    {
                        title: "Don't have flags",
                        component: "flags",
                        props: {
                            flags: window.library.skillFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || !element.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                ];
            },

            addSkill: function (item) {
                if (item.skills === undefined) {
                    item.skills = [];
                }

                item.skills.push({
                    id: this.skills[0] ? this.skills[0].id : undefined
                });
            },

            removeSkill: function (item, skill) {
                let index = item.skills.indexOf(skill);
                item.skills.splice(index, 1);
            },

            skillMoveUp: function (item, skill) {
                let index = item.skills.indexOf(skill);

                if (index === -1 || index === 0) {
                    return;
                }

                item.skills[index] = item.skills[index - 1];
                item.skills[index - 1] = skill;

                this.$forceUpdate();
            },

            skillMoveDown: function (item, skill) {
                let index = item.skills.indexOf(skill);

                if (index === -1 || index === item.skills.length) {
                    return;
                }

                item.skills[index] = item.skills[index + 1];
                item.skills[index + 1] = skill;

                this.$forceUpdate();
            },

            addPrice: function (skill) {
                skill.prices.push({
                    currency_id: this.currencies[0] ? this.currencies[0].id : undefined,
                    amount: 0
                });
            },

            removePrice: function (skill, price) {
                let index = skill.prices.indexOf(price);
                skill.prices.splice(index, 1);
            },

            select: function (skill) {
                this.selected = skill;
            },

            create: function () {
                this.skills.push({
                    fresh: true,
                    icon: "",
                    name_i18n: {en: "skill_"},
                    effect_id: this.effects[0] ? this.effects[0].id : undefined,
                    costs: [],
                    flags: []
                });

                this.select(this.skills[this.skills.length - 1]);
            },

            addCost: function (skill) {
                skill.costs.push({
                    resource_type: this.window.library.resourceTypes[0],
                    value: 0
                });
            },

            removeCost: function (skill, cost) {
                let index = skill.costs.indexOf(cost);
                skill.costs.splice(index, 1);
            },

            duplicate: function (skill) {
                let copy = Object.assign({}, skill);
                copy.fresh = true;
                copy.id = "";
                copy.name = copy.name + " copy";

                this.skills.push(copy);
                this.select(copy);
            },

            reset: function (skill) {
                if (skill.fresh) {
                    return;
                }

                let index = this.skills.indexOf(skill);

                window.axios.get("/frontend/skills/" + skill.id).then(response => {
                    this.skills[index] = response.data;
                    this.select(this.skills[index]);
                });
            },

            destroy: function (skill) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.skills.indexOf(skill);

                if (skill.fresh) {
                    this.skills.splice(index, 1);
                    this.selected = this.skills[index <= 0 ? 0 : index - 1];
                    return;
                }

                window.axios.delete("/frontend/skills/" + skill.id).then(response => {
                    this.skills.splice(index, 1);
                    this.selected = this.skills[index <= 0 ? 0 : index - 1];
                });
            },

            save: function (skill) {
                let index = this.skills.indexOf(skill);

                if (skill.fresh) {
                    window.axios.post("/frontend/skills", skill).then(response => {
                        this.skills[index] = response.data;
                        this.select(this.skills[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/skills/" + skill.id, skill).then(response => {
                    this.skills[index] = response.data;
                    this.select(this.skills[index]);
                });
            },

            saveAll: function () {
                for (let key in this.skills) {
                    let skill = this.skills[key];

                    if (skill.fresh) {
                        window.axios.post("/frontend/skills", skill);
                        continue;
                    }

                    window.axios.put("/frontend/skills/" + skill.id, skill);
                }

                this.load();
            }
        }
    };
</script>
