<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-4">
                    <list :elements="rewards" :fields="['id', 'label', 'type', getRewards]" v-model="selected"></list>
                </div>

                <div class="col-md-8">
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
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <selectpicker v-model="selected.type" :options="library.rewardTypes"></selectpicker>
                                    </td>
                                </tr>
                            </table>

                            <!-- TALENT POINTS -->
                            <table class="form-table" :class="selected.type !== 'TalentPointsReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Talent Points</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.talent_points">
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- ATTRIBUTE POINTS -->
                            <table class="form-table" :class="selected.type !== 'AttributePointsReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Attribute Points</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.attribute_points">
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- RANDOM SKILLS -->
                            <table class="form-table" :class="selected.type !== 'RandomSkillsUnlockReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Count</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.random_skills_count">
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- ITEMS -->
                            <table class="form-table" :class="selected.type !== 'ItemsReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Items</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="item in selected.items">
                                                <td style="width: 80%;">
                                                    <item-field v-model="item.id" :items="items"></item-field>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="item.pivot.amount">
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeItem(selected, item)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addItem(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CURRENCIES -->
                            <table class="form-table" :class="selected.type !== 'CurrenciesReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Items</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="currency in selected.currencies">
                                                <td style="width: 80%;">
                                                    <currency-field v-model="currency.id" :currencies="currencies"></currency-field>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="currency.pivot.amount">
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeCurrency(selected, currency)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addCurrency(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- ATTRIBUTES -->
                            <table class="form-table" :class="selected.type !== 'AttributesReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Attributes</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="attribute in selected.attributes">
                                                <td style="width: 80%;">
                                                    <selectpicker v-model="attribute.id" :options="attributes"
                                                                  :labelcallback="(attribute) => {return attribute.name_i18n.en}"
                                                                  :valuecallback="(attribute) => {return attribute.id}"></selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="attribute.pivot.amount">
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeAttribute(selected, attribute)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAttribute(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- PROPERTIES -->
                            <table class="form-table" :class="selected.type !== 'PropertiesReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Properties</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="property in selected.properties">
                                                <td style="width: 80%;">
                                                    <selectpicker v-model="property.id" :options="properties"
                                                                  :labelcallback="(property) => {return property.name_i18n.en}"
                                                                  :valuecallback="(property) => {return property.id}"></selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="property.pivot.amount">
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeProperty(selected, property)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addProperty(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- LEVEL UP -->
                            <table class="form-table" :class="selected.type !== 'LevelupReward' ? 'hidden' : ''">
                                <tr>
                                    <td>Level</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.level">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Rewards</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tbody>
                                            <tr v-for="reward in selected.rewards">
                                                <td style="width: 80%;">
                                                    <selectpicker v-model="reward.id"
                                                                  :options="rewards.filter((option) => {return option.id !== selected.id})"
                                                                  :labelcallback="(option) => {return option.label}"
                                                                  :valuecallback="(option) => {return option.id}">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeReward(selected, reward)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addReward(selected)">
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
    import {Events} from "./../events";

    export default {
        data() {
            return {
                rewards: [],
                items: [],
                currencies: [],
                attributes: [],
                properties: [],
                library: window.library,
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

        updated () {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        methods: {
            load: function () {
                window.axios.get("/frontend/rewards").then(response => {
                    this.rewards = response.data;
                    this.select(this.id === undefined ? this.rewards[0] : this.rewards.find((element) => element.id === this.id));
                });

                window.axios.get("/frontend/attributes").then(response => {
                    this.attributes = response.data;
                });

                window.axios.get("/frontend/properties").then(response => {
                    this.properties = response.data;
                });

                window.axios.get("/frontend/items").then(response => {
                    this.items = response.data;
                });

                window.axios.get("/frontend/currencies").then(response => {
                    this.currencies = response.data;
                });
            },

            getRewards: function (reward) {
                return '<span style="font-family: monospace;">' + reward.rewards.map(a => a.label).join(', ') + '</span>';
            },

            addItem: function (reward) {
                reward.items.push({
                    id: this.items[0] ? this.items[0].id : undefined,
                    pivot: {amount: 1}
                });
            },

            removeItem: function (reward, item) {
                let index = reward.items.indexOf(item);
                reward.items.splice(index, 1);
            },

            addCurrency: function (reward) {
                reward.currencies.push({
                    id: this.currencies[0] ? this.currencies[0].id : undefined,
                    pivot: {amount: 1}
                });
            },

            removeCurrency: function (reward, currency) {
                let index = reward.currencies.indexOf(currency);
                reward.currencies.splice(index, 1);
            },

            addAttribute: function (reward) {
                reward.attributes.push({
                    id: this.attributes[0] ? this.attributes[0].id : undefined,
                    pivot: {amount: 1}
                });
            },

            removeAttribute: function (reward, attribute) {
                let index = reward.attributes.indexOf(attribute);
                reward.attributes.splice(index, 1);
            },

            addProperty: function (reward) {
                reward.properties.push({
                    id: this.properties[0] ? this.properties[0].id : undefined,
                    pivot: {amount: 1}
                });
            },

            removeProperty: function (reward, property) {
                let index = reward.properties.indexOf(property);
                reward.properties.splice(index, 1);
            },

            addReward: function (reward) {
                reward.rewards.push({id: undefined});
            },

            removeReward: function (parent, child) {
                let index = parent.rewards.indexOf(child);
                parent.rewards.splice(index, 1);
            },

            create: function () {
                this.rewards.push({
                    fresh: true,
                    name_i18n: {en: "reward_"}
                });

                this.select(this.rewards[this.rewards.length - 1]);
            },

            select: function (reward) {
                this.selected = reward;
            },

            duplicate: function (reward) {
                let copy = Object.assign({}, reward);
                copy.fresh = true;
                this.rewards.push(copy);
                this.select(copy);
            },

            reset: function (reward) {
                if (reward.fresh) {
                    return;
                }

                let index = this.rewards.indexOf(reward);

                window.axios.get("/frontend/rewards/" + reward.id).then(response => {
                    this.rewards[index] = response.data;
                    this.select(this.rewards[index]);
                });
            },

            save: function (reward) {
                let index = this.rewards.indexOf(reward);

                if (reward.fresh) {
                    window.axios.post("/frontend/rewards", reward).then(response => {
                        this.rewards[index] = response.data;
                        this.select(this.rewards[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/rewards/" + reward.id, reward).then(response => {
                    this.rewards[index] = response.data;
                    this.select(this.rewards[index]);
                });
            },

            destroy: function (reward) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.rewards.indexOf(reward);

                if (reward.fresh) {
                    this.rewards.splice(index, 1);
                    this.select(this.rewards[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/rewards/" + reward.id).then(() => {
                    this.rewards.splice(index, 1);
                    this.select(this.rewards[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.rewards) {
                    let reward = this.rewards[key];

                    if (reward.fresh) {
                        window.axios.post("/frontend/rewards", reward).then(response => {
                            this.rewards[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/rewards/" + reward.id, reward).then(response => {
                        this.rewards[key] = response.data;
                    });
                }
            }
        }
    };
</script>
