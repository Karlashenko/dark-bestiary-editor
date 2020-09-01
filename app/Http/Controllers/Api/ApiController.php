<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Achievement;
use App\Models\Archetype;
use App\Models\Attribute;
use App\Models\Background;
use App\Models\Behaviour;
use App\Models\Currency;
use App\Models\Dialogue;
use App\Models\Effect;
use App\Models\Environment;
use App\Models\Food;
use App\Models\I18N;
use App\Models\Item;
use App\Models\BehaviourTree;
use App\Models\ItemCategory;
use App\Models\ItemModifier;
use App\Models\ItemRarity;
use App\Models\ItemSet;
use App\Models\ItemType;
use App\Models\Loot;
use App\Models\Mastery;
use App\Models\Missile;
use App\Models\Phrase;
use App\Models\Property;
use App\Models\Recipe;
use App\Models\Relic;
use App\Models\Reward;
use App\Models\Scenario;
use App\Models\Scene;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SkillSet;
use App\Models\SkillTree;
use App\Models\Skin;
use App\Models\Talent;
use App\Models\TalentCategory;
use App\Models\TalentCell;
use App\Models\Unit;
use App\Models\UnitGroup;
use App\Models\Validator;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DownloadsController
 *
 * @package App\Http\Controllers\Web
 */
class ApiController extends Controller
{
    private function response(array $data): Response
    {
        return \response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return Response
     */
    public function units(): Response
    {
        return $this->response(Unit::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function food(): Response
    {
        return $this->response(Food::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function masteries(): Response
    {
        return $this->response(Mastery::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function phrases(): Response
    {
        return $this->response(Phrase::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function relics(): Response
    {
        return $this->response(Relic::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function backgrounds(): Response
    {
        return $this->response(Background::enabled()->get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function unitGroups(): Response
    {
        return $this->response(UnitGroup::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function skillCategories(): Response
    {
        return $this->response(SkillCategory::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function talentCategories(): Response
    {
        return $this->response(TalentCategory::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function skins(): Response
    {
        return $this->response(Skin::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function itemModifiers(): Response
    {
        return $this->response(ItemModifier::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function archetypes(): Response
    {
        return $this->response(Archetype::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function scenes(): Response
    {
        return $this->response(Scene::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function achievements(): Response
    {
        return $this->response(Achievement::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function items(): Response
    {
        return $this->response(Item::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function itemTypes(): Response
    {
        return $this->response(ItemType::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function itemSets(): Response
    {
        return $this->response(ItemSet::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function skillSets(): Response
    {
        return $this->response(SkillSet::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function itemCategories(): Response
    {
        return $this->response(ItemCategory::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function itemRarities(): Response
    {
        return $this->response(ItemRarity::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function currencies(): Response
    {
        return $this->response(Currency::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function validators(): Response
    {
        return $this->response(Validator::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function scenarios(): Response
    {
        return $this->response(Scenario::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function effects(): Response
    {
        return $this->response(Effect::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function missiles(): Response
    {
        return $this->response(Missile::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function skills(): Response
    {
        return $this->response(Skill::enabled()->get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function attributes(): Response
    {
        return $this->response(Attribute::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function properties(): Response
    {
        return $this->response(Property::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function rewards(): Response
    {
        return $this->response(Reward::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function behaviours(): Response
    {
        return $this->response(Behaviour::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function recipes(): Response
    {
        return $this->response(Recipe::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function environments(): Response
    {
        return $this->response(Environment::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function ai(): Response
    {
        return $this->response(BehaviourTree::byType('Tree')->get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function loot(): Response
    {
        return $this->response(Loot::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function talents(): Response
    {
        return $this->response(Talent::get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function dialogues(): Response
    {
        return $this->response(Dialogue::orderBy('label')->get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function i18n(): Response
    {
        return $this->response(I18N::orderBy('key')->get()->present()->toArray());
    }

    /**
     * @return Response
     */
    public function i18nRu(): Response
    {
        $data = I18N::orderBy('key')
                    ->get()
                    ->keyBy('key')
                    ->map(function (I18N $i18n) {return str_replace('\\\\', '\\', $i18n->ru);})
                    ->toArray();

        return $this->response([
            'Name'        => 'ru-RU',
            'DisplayName' => 'Русский',
            'Data'        => $data,
        ]);
    }

    /**
     * @return Response
     */
    public function i18nEn(): Response
    {
        $data = I18N::orderBy('key')
                    ->get()
                    ->keyBy('key')
                    ->map(function (I18N $i18n) {return str_replace('\\\\', '\\', $i18n->en);})
                    ->toArray();

        return $this->response([
            'Name'        => 'en-US',
            'DisplayName' => 'English',
            'Data'        => $data,
        ]);
    }
}
