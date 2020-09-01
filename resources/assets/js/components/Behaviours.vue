<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-4">
                    <list :elements="behaviours" :fields="['id', 'name_i18n.en|label', 'suffix', 'type', 'duration']" :icon="'icon'" v-model="selected" :filters="getFilters()"></list>
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
                                        <input type="text" class="form-control" v-model="selected.label">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Suffix</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.suffix">
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
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Period</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.period">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Event Subject</td>
                                    <td>
                                        <selectpicker v-model="selected.on_event_subject" :options="window.library.behaviourSubjects"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Duration</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.duration">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Stack Count Max</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.stack_count_max">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Scale</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" min="0" class="form-control" v-model="selected.scale">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Tint</td>
                                    <td>
                                        <div class="form-group">
                                            <input style="display: inline; width: 30%;" type="text" class="form-control" v-model="selected.tint">
                                            <input style="display: inline; width: 48px;" type="color" class="form-control" v-model="selected.tint">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Caster Is Bearer</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.caster_is_bearer">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Attachments</td>
                                    <td>
                                        <table class="table-inner">
                                            <thead>
                                            <tr>
                                                <th>Point</th>
                                                <th>Prefab</th>
                                                <th>Original Scale</th>
                                            </tr>
                                            </thead>
                                            <tr v-for="attachment in selected.attachments">
                                                <td>
                                                    <selectpicker v-model="attachment.point" :options="window.library.attachmentPoints"></selectpicker>
                                                </td>

                                                <td>
                                                    <prefab-field v-model="attachment.prefab"></prefab-field>
                                                </td>

                                                <td style="text-align: center">
                                                    <div class="form-group">
                                                        <input type="checkbox" v-model="attachment.original_scale">
                                                    </div>
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

                                <tr>
                                    <td>Validators</td>
                                    <td>
                                        <table class="table-inner">
                                            <thead>
                                            <tr>
                                                <th>Validator</th>
                                                <th>Purpose</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tr v-for="validator in selected.validators">
                                                <td>
                                                    <selectpicker v-model="validator.pivot.validator_id"
                                                                  :options="validators"
                                                                  :valuecallback="(element) => element.id"
                                                                  :labelcallback="(element) => element.name">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <selectpicker v-model="validator.pivot.validator_purpose"
                                                                  :options="window.library.validatorPurposes"
                                                                  :nullable="true">
                                                    </selectpicker>
                                                </td>

                                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                                    <button class="btn btn-default" @click="validatorMoveUp(selected, validator)">
                                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="validatorMoveDown(selected, validator)">
                                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="removeValidator(selected, validator)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="3">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addValidator(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.type" :options="window.library.behaviourTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>On Expire Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_expire_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>On Remove Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_remove_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- BUFF -->
                            <table class="form-table" :class="selected.type !== 'BuffBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Initial Effect</td>
                                    <td>
                                        <effect-field v-model="selected.initial_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Periodic Effect</td>
                                    <td>
                                        <effect-field v-model="selected.periodic_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- SET -->
                            <table class="form-table" :class="selected.type !== 'SetBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Behaviours</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="behaviour in selected.behaviours">
                                                <td style="width: 90%;">
                                                    <behaviour-field v-model="behaviour.id" :behaviours="behaviours"></behaviour-field>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeBehaviour(selected, behaviour)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addBehaviour(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- MODIFY STATS -->
                            <table class="form-table" :class="selected.type !== 'ModifyStatsBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Attribute Modifiers</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th style="width: 20%">Attribute</th>
                                                <th style="width: 15%">Amount</th>
                                                <th style="width: 20%">Modifier</th>
                                                <th style="width: 15%">Fraction</th>
                                                <th style="width: 20%">Attribute</th>
                                                <th style="width: 15%">Max Attribute Fraction</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="modifier in selected.attribute_modifiers">
                                                    <td>
                                                        <selectpicker v-model="modifier.attribute_id"
                                                                      :nullable="true"
                                                                      :options="attributes"
                                                                      :valuecallback="(attribute) => attribute.id"
                                                                      :labelcallback="(attribute) => attribute.name_i18n.en">
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
                                                        <selectpicker v-model="modifier.fraction_attribute_id"
                                                                      :nullable="true"
                                                                      :options="attributes"
                                                                      :valuecallback="(attribute) => attribute.id"
                                                                      :labelcallback="(attribute) => attribute.name_i18n.en">
                                                        </selectpicker>
                                                    </td>

                                                    <td>
                                                        <input type="number" class="form-control" v-model="modifier.max_attribute_fraction">
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
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Property Modifiers</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th style="width: 20%">Property</th>
                                                <th style="width: 10%">Amount</th>
                                                <th style="width: 10%">Modifier</th>
                                                <th style="width: 10%">Fraction</th>
                                                <th style="width: 20%">Property</th>
                                                <th style="width: 20%">Attribute</th>
                                                <th style="width: 10%">Max Attribute Fraction</th>
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
                                                    <input type="number" class="form-control" v-model="modifier.max_attribute_fraction">
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

                            <!-- SHIELD -->
                            <table class="form-table" :class="selected.type !== 'ShieldBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Max Amount Formula</td>
                                    <td>
                                        <textarea class="form-control" v-model="selected.shield_max_amount_formula" style="font-family:monospace;"></textarea>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON TAKE HEAL -->
                            <table class="form-table" :class="selected.type !== 'OnTakeHealBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_take_heal_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON COMBAT START -->
                            <table class="form-table" :class="selected.type !== 'OnCombatStartBehaviour' && selected.type !== 'OnScenarioStartBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_combat_start_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON END TURN -->
                            <table class="form-table" :class="selected.type !== 'OnEndTurnBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_end_turn_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- UNLOCK SKILL -->
                            <table class="form-table" :class="selected.type !== 'UnlockSkillBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Skill</td>
                                    <td>
                                        <skill-field v-model="selected.unlock_skill_id" :skills="skills"></skill-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON CONTACT -->
                            <table class="form-table" :class="selected.type !== 'OnContactBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Radius</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.on_contact_radius">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_contact_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON DEAL HEAL -->
                            <table class="form-table" :class="selected.type !== 'OnDealHealBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_deal_heal_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON TAKE DAMAGE -->
                            <table class="form-table" :class="selected.type !== 'OnTakeDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Damage Types</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_types"
                                                      :nullable="true"
                                                      :multiple="true"
                                                      :selectcallback="(type) => selected.damage_types.includes(type)"
                                                      :options="window.library.damageTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Flags</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th style="text-align: left;">Include</th>
                                                <th style="text-align: left;">Exclude</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <flags v-model="selected.damage_flags" :flags="window.library.damageFlags"></flags>
                                                </td>
                                                <td>
                                                    <flags v-model="selected.damage_flags_exclude" :flags="window.library.damageFlags"></flags>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Info Flags</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead style="text-align: left;">
                                            <tr>
                                                <th style="text-align: left;">Include</th>
                                                <th style="text-align: left;">Exclude</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <flags v-model="selected.damage_info_flags" :flags="window.library.damageInfoFlags"></flags>
                                                </td>
                                                <td>
                                                    <flags v-model="selected.damage_info_flags_exclude" :flags="window.library.damageInfoFlags"></flags>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_take_damage_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON DEAL DAMAGE -->
                            <table class="form-table" :class="selected.type !== 'OnDealDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Damage Types</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_types"
                                                      :nullable="true"
                                                      :multiple="true"
                                                      :selectcallback="(type) => selected.damage_types.includes(type)"
                                                      :options="window.library.damageTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Flags</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead>
                                            <tr>
                                                <th style="text-align: left;">Include</th>
                                                <th style="text-align: left;">Exclude</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <flags v-model="selected.damage_flags" :flags="window.library.damageFlags"></flags>
                                                </td>
                                                <td>
                                                    <flags v-model="selected.damage_flags_exclude" :flags="window.library.damageFlags"></flags>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Info Flags</td>
                                    <td>
                                        <table style="width: 100%;">
                                            <thead style="text-align: left;">
                                            <tr>
                                                <th style="text-align: left;">Include</th>
                                                <th style="text-align: left;">Exclude</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <flags v-model="selected.damage_info_flags" :flags="window.library.damageInfoFlags"></flags>
                                                </td>
                                                <td>
                                                    <flags v-model="selected.damage_info_flags_exclude" :flags="window.library.damageInfoFlags"></flags>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_deal_damage_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON EPISODE START -->
                            <table class="form-table" :class="selected.type !== 'OnEpisodeStartBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_episode_start_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON EPISODE COMPLETE -->
                            <table class="form-table" :class="selected.type !== 'OnEpisodeCompleteBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_episode_complete_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON EXIT CELL -->
                            <table class="form-table" :class="selected.type !== 'OnExitCellBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_exit_cell_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON ENTER CELL -->
                            <table class="form-table" :class="selected.type !== 'OnEnterCellBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_enter_cell_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON DEATH -->
                            <table class="form-table" :class="selected.type !== 'OnDeathBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_death_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON BLOCK -->
                            <table class="form-table" :class="selected.type !== 'OnBlockBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_block_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON USE SKILL -->
                            <table class="form-table" :class="selected.type !== 'OnUseSkillBehaviour' &&
                                                              selected.type !== 'OnUsingSkillBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Skill Rarity</td>
                                    <td>
                                        <selectpicker v-model="selected.on_use_skill_rarity_id"
                                                      :options="rarities"
                                                      :nullable="true"
                                                      :valuecallback="(element) => element.id"
                                                      :labelcallback="(element) => element.name_i18n.en">
                                        </selectpicker>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Skill Flags</td>
                                    <td>
                                        <flags v-model="selected.on_use_skill_flags" :flags="window.library.skillFlags"></flags>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_use_skill_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON HEALTH DROPS BELOW -->
                            <table class="form-table" :class="selected.type !== 'OnHealthDropsBelowBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Fraction</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.on_health_drops_below_fraction">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_health_drops_below_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- SPIRIT LINK -->
                            <table class="form-table" :class="selected.type !== 'SpiritLinkBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Fraction</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.spirit_link_fraction">
                                    </td>
                                </tr>
                            </table>

                            <!-- ON STATUS EFFECT REMOVED -->
                            <table class="form-table" :class="selected.type !== 'OnStatusEffectRemovedBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Status Flags</td>
                                    <td>
                                        <flags v-model="selected.on_status_effect_removed_status_flags" :flags="window.library.behaviourStatusFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_status_effect_removed_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON STATUS EFFECT -->
                            <table class="form-table" :class="selected.type !== 'OnStatusEffectBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Status Flags</td>
                                    <td>
                                        <flags v-model="selected.on_status_effect_status_flags" :flags="window.library.behaviourStatusFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.on_status_effect_behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- ON KILL OR ANYONE DIED -->
                            <table class="form-table" :class="selected.type !== 'OnKillBehaviour' && selected.type !== 'OnAnyoneDiedBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Damage Flags</td>
                                    <td>
                                        <flags v-model="selected.damage_flags" :flags="window.library.damageFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Info Flags</td>
                                    <td>
                                        <flags v-model="selected.damage_info_flags" :flags="window.library.damageInfoFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.on_kill_effect_id" :effects="effects"></effect-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- CHANGE MODEL -->
                            <table class="form-table" :class="selected.type !== 'ChangeModelBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Model</td>
                                    <td>
                                        <prefab-field v-model="selected.change_model"></prefab-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- SPHERES -->
                            <table class="form-table" :class="selected.type !== 'SpheresBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Prefab</td>
                                    <td>
                                        <prefab-field v-model="selected.spheres_prefab"></prefab-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type && selected.type.match('.*DamageBehaviour') && !selected.type.match('On.') ? '' : 'hidden'">
                                <tr>
                                    <td>Modifier</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_modifier_type" :options="window.library.modifierTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Types</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_types"
                                                      :nullable="true"
                                                      :multiple="true"
                                                      :selectcallback="(type) => selected.damage_types.includes(type)"
                                                      :options="window.library.damageTypes"></selectpicker>
                                    </td>
                                </tr>
                            </table>

                            <!-- CLEAVE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'CleaveBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Fraction</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.cleave_fraction">
                                    </td>
                                </tr>
                            </table>

                            <!-- ITEM BASED BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'ItemBasedBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.item_based_behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Item Category</td>
                                    <td>
                                        <selectpicker v-model="selected.item_based_category_id"
                                                      :nullable="true"
                                                      :options="itemCategories"
                                                      :valuecallback="(itemCategory) => itemCategory.id"
                                                      :labelcallback="(itemCategory) => itemCategory.type">
                                        </selectpicker>
                                    </td>
                                </tr>
                            </table>

                            <!-- DUAL WIELD BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'DualWieldBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.dual_wield_behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- CREATE LINE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'CreateLineBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Prefab</td>
                                    <td>
                                        <prefab-field v-model="selected.create_line_prefab"/>
                                    </td>
                                </tr>
                            </table>

                            <!-- AURA BEHAVIOUR -->
                            <table class="form-table" :class="selected.type === 'AuraBehaviour' || selected.type === 'PerUnitInRangeBehaviour' ? '' : 'hidden'">
                                <tr>
                                    <td>Range</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.aura_range">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.aura_behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>
                            </table>

                            <!-- PER SURROUNDING ENEMY DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'PerSurroundingEnemyDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Radius</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_value">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Minimum number of enemies</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_minimum_number_of_enemies">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Amount per enemy</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Min</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_min">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Max</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_max">
                                    </td>
                                </tr>
                            </table>

                            <!-- HEALTH PERCENTAGE DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'HealthFractionDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Target</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_target" :options="window.library.damageTargets"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Comparator</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_comparator" :options="window.library.comparatorTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Required Health Fraction</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_value">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Amount</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>
                            </table>

                            <!-- PER MISSING HEALTH DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'PerMissingHealthPercentDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Target</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_target" :options="window.library.damageTargets"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Amount per missing health percent</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Min</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_min">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Max</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_max">
                                    </td>
                                </tr>
                            </table>

                            <!-- RANGE DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'RangeDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Range</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_value">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Comparator</td>
                                    <td>
                                        <selectpicker v-model="selected.damage_comparator" :options="window.library.comparatorTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Amount</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>
                            </table>

                            <!-- STATUS EFFECT DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'StatusEffectDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Amount</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Status Flags</td>
                                    <td>
                                        <flags v-model="selected.damage_status_flags" :flags="window.library.behaviourStatusFlags"></flags>
                                    </td>
                                </tr>
                            </table>

                            <!-- BACKSTAB DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'BackstabDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Amount</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>
                            </table>

                            <!-- SUMMONED DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'SummonedDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Amount</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>
                            </table>

                            <!-- PER RANGE DAMAGE BEHAVIOUR -->
                            <table class="form-table" :class="selected.type !== 'PerRangeDamageBehaviour' ? 'hidden' : ''">
                                <tr>
                                    <td>Amount per cell</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_amount">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Min</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_min">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Max</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.damage_max">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Flags</td>
                                    <td>
                                        <flags v-model="selected.damage_flags" :flags="window.library.damageFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Damage Info Flags</td>
                                    <td>
                                        <flags v-model="selected.damage_info_flags" :flags="window.library.damageInfoFlags"></flags>
                                    </td>
                                </tr>
                            </table>

                            <table class="form-table">
                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Flags</td>
                                    <td>
                                        <flags v-model="selected.flags" :flags="window.library.behaviourFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Re-apply Flags</td>
                                    <td>
                                        <flags v-model="selected.re_apply_flags" :flags="window.library.behaviourReApplyFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Status Flags</td>
                                    <td>
                                        <flags v-model="selected.status_flags" :flags="window.library.behaviourStatusFlags"></flags>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Status Immunity</td>
                                    <td>
                                        <flags v-model="selected.status_immunity" :flags="window.library.behaviourStatusFlags"></flags>
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
                behaviours: [],
                itemCategories: [],
                effects: [],
                skills: [],
                attributes: [],
                properties: [],
                rarities: [],
                window: window,
                selected: undefined
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

        methods: {
            getFilters: function () {
                return [
                    {
                        title: "Type",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.window.library.behaviourTypes
                        },
                        filter: (element, value) => {
                            return !value || element.type === value
                        },
                        value: null
                    },
                    {
                        title: "Flags",
                        component: "flags",
                        props: {
                            flags: window.library.behaviourFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                    {
                        title: "Status Flags",
                        component: "flags",
                        props: {
                            flags: window.library.behaviourStatusFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.status_flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                ];
            },

            addAttachment: function (behaviour) {
                behaviour.attachments.push({
                    point: window.library.attachmentPoints[0]
                });
            },

            removeAttachment: function (behaviour, attachment) {
                let index = behaviour.attachments.indexOf(attachment);
                behaviour.attachments.splice(index, 1);
            },

            addValidator: function (item) {
                if (item.validators === undefined) {
                    Object.assign(item, {validators: []});
                }

                item.validators.push({
                    pivot: {
                        validator_id: this.validators[0] ? this.validators[0].id : undefined,
                        validator_purpose: window.library.validatorPurposes[0],
                    }
                });
            },

            removeValidator: function (item, validator) {
                let index = item.validators.indexOf(validator);
                item.validators.splice(index, 1);
            },

            validatorMoveUp: function (item, validator) {
                let index = item.validators.indexOf(validator);

                if (index === -1 || index === 0) {
                    return;
                }

                item.validators[index] = item.validators[index - 1];
                item.validators[index - 1] = validator;

                this.$forceUpdate();
            },

            validatorMoveDown: function (item, validator) {
                let index = item.validators.indexOf(validator);

                if (index === -1 || index === item.validators.length) {
                    return;
                }

                item.validators[index] = item.validators[index + 1];
                item.validators[index + 1] = validator;

                this.$forceUpdate();
            },

            load: function () {
                window.axios.get('/frontend/behaviours').then(response => {
                    this.behaviours = response.data;
                    this.select(this.id === undefined ? this.behaviours[0] : this.behaviours.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/effects').then(response => {
                    this.effects = response.data;
                });

                window.axios.get('/frontend/attributes').then(response => {
                    this.attributes = response.data;
                });

                window.axios.get('/frontend/properties').then(response => {
                    this.properties = response.data;
                });

                window.axios.get("/frontend/item_categories").then(response => {
                    this.itemCategories = response.data;
                });

                window.axios.get("/frontend/validators").then(response => {
                    this.validators = response.data;
                });

                window.axios.get("/frontend/skills").then(response => {
                    this.skills = response.data;
                });

                window.axios.get("/frontend/item_rarities").then(response => {
                    this.rarities = response.data;
                });
            },

            addBehaviour: function (behaviour) {
                behaviour.behaviours.push({
                    id: this.behaviours[0] ? this.behaviours[0].id : undefined
                });
            },

            removeBehaviour: function (behaviour, child) {
                behaviour.behaviours.splice(behaviour.behaviours.indexOf(child), 1);
            },

            create: function () {
                this.behaviours.push({
                    fresh: true,
                    name_i18n: {en: "behaviour_"},
                    attribute_modifiers: [],
                    property_modifiers: [],
                    scale: 1,
                    status_flags: [],
                    status_immunity: [],
                    damage_flags: [],
                    damage_status_flags: [],
                    damage_flags_exclude: [],
                    damage_info_flags: [],
                    damage_info_flags_exclude: [],
                    on_status_effect_status_flags: [],
                    on_status_effect_removed_status_flags: [],
                    on_use_skill_flags: [],
                    re_apply_flags: [],
                    behaviours: [],
                    flags: []
                });

                this.select(this.behaviours[this.behaviours.length - 1]);
            },

            select: function (behaviour) {
                this.selected = behaviour;
            },

            duplicate: function (behaviour) {
                let copy = Object.assign({}, behaviour);
                copy.fresh = true;
                this.behaviours.push(copy);
                this.select(copy);
            },

            reset: function (behaviour) {
                if (behaviour.fresh) {
                    return;
                }

                let index = this.behaviours.indexOf(behaviour);

                window.axios.get('/frontend/behaviours/' + behaviour.id).then(response => {
                    this.behaviours[index] = response.data;
                    this.select(this.behaviours[index]);
                });
            },

            addAttributeModifier: function (item) {
                item.attribute_modifiers.push({
                    attribute_id: this.attributes[0] ? this.attributes[0].id : undefined,
                    type: window.library.modifierTypes[0],
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
                    type: window.library.modifierTypes[0],
                    value: 0
                });
            },

            removePropertyModifier: function (item, modifier) {
                let index = item.property_modifiers.indexOf(modifier);
                item.property_modifiers.splice(index, 1);
            },

            save: function (behaviour) {
                let index = this.behaviours.indexOf(behaviour);

                if (behaviour.fresh) {
                    window.axios.post('/frontend/behaviours', behaviour).then(response => {
                        this.behaviours[index] = response.data;
                        this.select(this.behaviours[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/behaviours/' + behaviour.id, behaviour).then(response => {
                    this.behaviours[index] = response.data;
                    this.select(this.behaviours[index]);
                });
            },

            saveAll: function () {
                for (let key in this.behaviours) {
                    let behaviour = this.behaviours[key];

                    if (behaviour.fresh) {
                        window.axios.post('/frontend/behaviours', behaviour).then(response => {
                            this.behaviours[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/behaviours/' + behaviour.id, behaviour).then(response => {
                        this.behaviours[key] = response.data;
                    });
                }
            },

            destroy: function (behaviour) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.behaviours.indexOf(behaviour);

                if (behaviour.fresh) {
                    this.behaviours.splice(index, 1);
                    this.select(this.behaviours[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/behaviours/' + behaviour.id).then(response => {
                    this.behaviours.splice(index, 1);
                    this.select(this.behaviours[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>
