<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-4">
                    <list :elements="items" :fields="['id', 'name_i18n.en', 'type.type', 'slot', 'rarity.type', 'level', 'stack_size', 'suffix.suffix_i18n.en']" :icon="'icon'" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-8">
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
                                    <td>Level</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.level">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Enabled</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_enabled"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Comment</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.comment"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled />
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
                                    <td>Passive Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.passive_description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consume Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.consume_description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Lore I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.lore_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Book Text</td>
                                    <td>
                                        <i18n-field v-model="selected.book_text_i18n_id"></i18n-field>
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

                                <tr>
                                    <td>Suffix</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.suffix_id"
                                                          :nullable="true"
                                                          :options="itemModifiers"
                                                          :valuecallback="(element) => element.id"
                                                          :labelcallback="(element) => window.getPropertyByPath(element, 'suffix_i18n.en|label')">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td style="white-space: nowrap;">Modifiers</td>
                                    <td style="width: 100%">
                                        <table style="width: 100%; table-layout: fixed;">
                                            <thead>
                                            <tr>
                                                <th style="width: 80%;">Modifier</th>
                                                <th>Is Fixed</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="itemModifier in selected.item_modifiers">
                                                <td>
                                                    <selectpicker v-model="itemModifier.id"
                                                                  :nullable="true"
                                                                  :options="itemModifiers"
                                                                  :valuecallback="(element) => element.id"
                                                                  :labelcallback="(element) => window.getPropertyByPath(element, 'suffix_i18n.en|label')">
                                                    </selectpicker>
                                                </td>

                                                <td style="text-align: center">
                                                    <input type="checkbox" v-model="itemModifier.pivot.is_fixed">
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
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Attribute Modifiers</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <tr v-for="modifier in selected.attribute_modifiers">
                                                <td style="width: 50%;">
                                                    <selectpicker v-model="modifier.attribute_id"
                                                                  :options="attributes"
                                                                  :valuecallback="(attribute) => attribute.id"
                                                                  :labelcallback="(attribute) => attribute.type">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="modifier.amount">
                                                </td>

                                                <td>
                                                    <selectpicker v-model="modifier.type" :options="library.modifierTypes"></selectpicker>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeAttributeModifier(selected, modifier)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="3">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAttributeModifier(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Property Modifiers</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <tr v-for="modifier in selected.property_modifiers">
                                                <td style="width: 50%;">
                                                    <selectpicker v-model="modifier.property_id"
                                                                  :options="properties"
                                                                  :valuecallback="(property) => property.id"
                                                                  :labelcallback="(property) => property.type">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="modifier.amount">
                                                </td>

                                                <td>
                                                    <selectpicker v-model="modifier.type" :options="library.modifierTypes"></selectpicker>
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
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Skin</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.skin_id"
                                                          :nullable="true"
                                                          :options="skins"
                                                          :valuecallback="(skin) => skin.id"
                                                          :labelcallback="(skin) => skin.label">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Attachments</td>
                                    <td>
                                        <table class="table-inner">
                                            <tr v-for="attachment in selected.attachments">
                                                <td>
                                                    <selectpicker v-model="attachment.point" :options="library.attachmentPoints"></selectpicker>
                                                </td>

                                                <td>
                                                    <prefab-field v-model="attachment.prefab"></prefab-field>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeAttachment(selected, attachment)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="3">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAttachment(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Flags</td>
                                    <td>
                                        <flags v-model="selected.flags" :flags="library.itemFlags"></flags>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Set</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.set_id"
                                                          :nullable="true"
                                                          :options="itemSets"
                                                          :valuecallback="(itemSet) => itemSet.id"
                                                          :labelcallback="(itemSet) => itemSet.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Rarity</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.rarity_id"
                                                          :options="itemRarities"
                                                          :valuecallback="(itemRarity) => itemRarity.id"
                                                          :labelcallback="(itemRarity) => itemRarity.type">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.type_id"
                                                          :options="itemTypes"
                                                          :valuecallback="(itemType) => itemType.id"
                                                          :labelcallback="(itemType) => itemType.type">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Slot</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.slot" :options="library.itemSlotTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Weapon Skill A</td>
                                    <td>
                                        <div class="form-group">
                                            <skill-field v-model="selected.weapon_skill_1_id" :skills="skills"></skill-field>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Weapon Skill B</td>
                                    <td>
                                        <div class="form-group">
                                            <skill-field v-model="selected.weapon_skill_2_id" :skills="skills"></skill-field>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Consume Cooldown</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.consume_cooldown">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consume Sound</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.consume_sound">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consume Loot</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.consume_loot_id"
                                                          :options="lootTables"
                                                          :nullable="true"
                                                          :valuecallback="(lootTable) => lootTable.id"
                                                          :labelcallback="(lootTable) => lootTable.name">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Consume Effect</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.consume_effect_id"
                                                          :options="effects"
                                                          :nullable="true"
                                                          :valuecallback="(effect) => effect.id"
                                                          :labelcallback="(effect) => effect.name">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Unlock Relic</td>
                                    <td>
                                        <div class="form-group">
                                            <relic-field v-model="selected.unlock_relic_id" :relics="relics"></relic-field>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Unlock Skill</td>
                                    <td>
                                        <div class="form-group">
                                            <skill-field v-model="selected.unlock_skill_id" :skills="skills"></skill-field>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Unlock Scenario</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.unlock_scenario_id"
                                                          :options="scenarios"
                                                          :nullable="true"
                                                          :valuecallback="(scenario) => scenario.id"
                                                          :labelcallback="(scenario) => scenario.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Unlock Recipe</td>
                                    <td>
                                        <div class="form-group">
                                            <recipe-field v-model="selected.blueprint_recipe.id" :nullable="true" :recipes="recipes"></recipe-field>
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Level</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.level">
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Socket Count</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.socket_count">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Stack Size</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.stack_size">
                                        </div>
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Enchantment: Item Category</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.enchantment_item_category_id"
                                                          :options="itemCategories"
                                                          :valuecallback="(itemCategory) => itemCategory.id"
                                                          :labelcallback="(itemCategory) => itemCategory.type">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Enchantment: Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.enchantment_behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Behaviours</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="behaviour in selected.behaviours">
                                                <td style="width: 70%;">
                                                    <behaviour-field v-model="behaviour.id" :behaviours="behaviours"></behaviour-field>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeBehaviour(selected, behaviour)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addBehaviour(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Currency Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.currency_type" :nullable="true" :options="library.currencyTypes"></selectpicker>
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
    import { Events } from './../events';

    export default {
        data() {
            return {
                loading: false,
                items: [],
                recipes: [],
                relics: [],
                skills: [],
                skins: [],
                effects: [],
                attributes: [],
                properties: [],
                currencies: [],
                behaviours: [],
                lootTables: [],
                itemCategories: [],
                itemModifiers: [],
                itemSets: [],
                itemTypes: [],
                itemRarities: [],
                window: window,
                library: window.library,
                selected: undefined,
            };
        },

        mounted() {
            this.load();

            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);
        },

        updated () {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        methods: {
            load: function () {
                this.loading = true;

                window.axios.get('/frontend/items').then(response => {
                    this.items = response.data;
                    this.select(this.id === undefined ? this.items[0] : this.items.find((element) => element.id === this.id));
                }).then(() => {
                    window.axios.get('/frontend/attributes').then(response => {
                        this.attributes = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/item_categories').then(response => {
                        this.itemCategories = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/loot').then(response => {
                        this.lootTables = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/effects').then(response => {
                        this.effects = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/item_sets').then(response => {
                        this.itemSets = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/item_types').then(response => {
                        this.itemTypes = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/item_rarities').then(response => {
                        this.itemRarities = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/properties').then(response => {
                        this.properties = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/currencies').then(response => {
                        this.currencies = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/item_modifiers').then(response => {
                        this.itemModifiers = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/behaviours').then(response => {
                        this.behaviours = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/skins').then(response => {
                        this.skins = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/recipes').then(response => {
                        this.recipes = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/scenarios').then(response => {
                        this.scenarios = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/relics').then(response => {
                        this.relics = response.data;
                    });
                }).then(() => {
                    window.axios.get('/frontend/skills').then(response => {
                        this.skills = response.data;
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
                            options: this.itemTypes,
                            valuecallback: (type) => type.id,
                            labelcallback: (type) => type.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.type_id === value
                        },
                        value: null
                    },
                    {
                        title: "Slot",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.library.itemSlotTypes,
                        },
                        filter: (element, value) => {
                            return !value || element.slot === value
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
                        title: "Category",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.itemCategories,
                            labelcallback: (category) => category.name_i18n.en
                        },
                        filter: (element, category) => {
                            if (category === null) {
                                return true;
                            }

                            for (let key in category.types) {
                                if (category.types[key].id == element.type_id) {
                                    return true;
                                }
                            }

                            return false;
                        },
                        value: null
                    },
                    {
                        title: "Set",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.itemSets,
                            valuecallback: (set) => set.id,
                            labelcallback: (set) => set.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.set_id === value
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
                        title: "Has Default Modifiers",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || (element.item_modifiers.length > 0) === value;
                        },
                        value: null
                    },
                    {
                        title: "Have Flags",
                        component: "flags",
                        props: {
                            flags: window.library.itemFlags
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
                            flags: window.library.itemFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || !element.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                ];
            },

            select: function (item) {
                this.selected = item;
            },

            getWeaponDamagePerLevelLabel: function(value) {
                switch (value) {
                    case 3: return "Low";
                    case 7.5: return "Medium";
                    case 15: return "High";
                }
            },

            getWeaponDamageVarianceLabel: function(value) {
                if (isNaN(value)) {
                    value = value.weapon_damage_variance;
                }

                value = parseFloat(value).toFixed(3);

                switch (value) {
                    case (1/4).toFixed(3): return "1d4";
                    case (1/6).toFixed(3): return "1d6";
                    case (1/8).toFixed(3): return "1d8";
                    case (1/10).toFixed(3): return "1d10";
                    case (1/12).toFixed(3): return "1d12";
                    case (1/20).toFixed(3): return "1d20";
                }

                return "";
            },

            addItemModifier: function (itemModifier) {
                if (itemModifier.item_modifiers === undefined) {
                    itemModifier.item_modifiers = [];
                }

                itemModifier.item_modifiers.push({
                    id: this.itemModifiers[0] ? this.itemModifiers[0].id : undefined,
                    pivot: {}
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

                if (index === -1 || index === itemModifier.item_modifiers.length) {
                    return;
                }

                itemModifier.item_modifiers[index] = itemModifier.item_modifiers[index + 1];
                itemModifier.item_modifiers[index + 1] = element;

                this.$forceUpdate();
            },

            create: function () {
                this.items.push({
                    fresh: true,
                    name_i18n: {},
                    blueprint_recipe: {},
                    attribute_modifiers: [],
                    property_modifiers: [],
                    attachments: [],
                    prices: [],
                    flags: [],
                    icon: "",
                    slot: this.library.itemSlotTypes[0],
                    rarity: this.itemRarities[0]
                });

                this.select(this.items[this.items.length - 1]);
            },

            duplicate: function (item) {
                let copy = Object.assign({}, item);
                copy.fresh = true;
                copy.id = '';

                this.items.push(copy);
                this.select(copy);
            },

            reset: function (item) {
                if (item.fresh) {
                    return;
                }

                let index = this.items.indexOf(item);

                window.axios.get('/frontend/items/' + item.id).then(response => {
                    this.items[index] = response.data;
                    this.select(this.items[index]);
                });
            },

            destroy: function (item) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.items.indexOf(item);

                if (item.fresh) {
                    this.items.splice(index, 1);
                    this.selected = this.items[index <= 0 ? 0 : index - 1];
                    return;
                }

                window.axios.delete('/frontend/items/' + item.id).then(response => {
                    this.items.splice(index, 1);
                    this.selected = this.items[index <= 0 ? 0 : index - 1];
                });
            },

            save: function (item) {
                let index = this.items.indexOf(item);

                if (item.fresh) {
                    window.axios.post('/frontend/items', item).then(response => {
                        this.items[index] = response.data;
                        this.select(this.items[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/items/' + item.id, item).then(response => {
                    this.items[index] = response.data;
                    this.select(this.items[index]);
                });
            },

            addAttachment: function (item) {
                item.attachments.push({
                    point: this.library.attachmentPoints[0]
                });
            },

            removeAttachment: function (item, attachment) {
                let index = item.attachments.indexOf(attachment);
                item.attachments.splice(index, 1);
            },

            addBehaviour: function (item) {
                item.behaviours.push({
                    id: this.behaviours[0] ? this.behaviours[0].id : undefined
                });
            },

            removeBehaviour: function (item, behaviour) {
                let index = item.behaviours.indexOf(behaviour);
                item.behaviours.splice(index, 1);
            },

            addAttributeModifier: function (item) {
                item.attribute_modifiers.push({
                    attribute_id: this.attributes[0] ? this.attributes[0].id : undefined,
                    type: this.library.modifierTypes[0],
                    value: 0
                });
            },

            removeAttributeModifier: function (item, modifier) {
                let index = item.attribute_modifiers.indexOf(modifier);
                item.attribute_modifiers.splice(index, 1);
            },

            addPropertyModifier: function (item) {
                item.property_modifiers.push({
                    property_id: this.properties[0] ? this.properties[0].id : undefined,
                    type: this.library.modifierTypes[0],
                    value: 0
                });
            },

            removePropertyModifier: function (item, modifier) {
                let index = item.property_modifiers.indexOf(modifier);
                item.property_modifiers.splice(index, 1);
            },

            addPrice: function (item) {
                item.prices.push({
                    currency_id: this.currencies[0] ? this.currencies[0].id : undefined,
                    amount: 0
                });
            },

            removePrice: function (item, price) {
                let index = item.prices.indexOf(price);
                item.prices.splice(index, 1);
            },

            saveAll: function () {
                for (let key in this.items) {
                    let item = this.items[key];

                    if (item.fresh) {
                        window.axios.post('/frontend/items', item);
                        continue;
                    }

                    window.axios.put('/frontend/items/' + item.id, item);
                }

                this.load();
            }
        }
    };
</script>
