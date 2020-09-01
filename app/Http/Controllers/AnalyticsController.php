<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Relic;
use App\Models\Skill;
use App\Models\Talent;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class AnalyticsController
 *
 * @package App\Http\Controllers
 */
class AnalyticsController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return \view('analytics.index');
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function store(Request $request): View
    {
        $events = new Collection(json_decode($request->data));

        if (false) {
            $events = $events->filter(function ($event) {
                return $event->steam_user_id === '76561198033822056';
            });
        }

        if (false) {
            $events = $events->filter(function ($event) {
                return $event->event === 'db_character_died';
            });
        }

        return \view('analytics.result')->with('data', [
            'skills'  => $this->CollectSkillInfo($events),
            'talents' => $this->CollectTalentInfo($events),
            'relics'  => $this->CollectRelicInfo($events),
        ]);
    }

    /**
     * @param Collection $events
     * @param Collection $talents
     *
     * @return Collection
     */
    private function CollectSkillInfo(Collection $events): Collection
    {
        $skills = new Collection();

        foreach ($events as $event) {
            if (!property_exists($event->data, 'Skills')) {
                continue;
            }

            foreach ($event->data->Skills as $skillId) {
                $skills->push($skillId);
            }
        }

        $skills = $skills->mapToGroups(function ($element) {
            return [$element => $element];
        })->map(function ($value, $key) {
            return [
                'skill' => Skill::whereType('Common')->find($key),
                'count' => count($value),
            ];
        })->filter(function ($info) {
            return $info['skill'] !== null;
        })->sortByDesc(function ($info) {
            return $info['count'];
        });

        $skillIds = $skills->keys()->toArray();

        foreach (Skill::whereType('Common')->whereIsEnabled(true)->get() as $skill) {
            if (in_array($skill->id, $skillIds) || in_array('Monster', $skill->flags)) {
                continue;
            }

            $skills[$skill->id] = ['skill' => $skill, 'count' => 0];
        }

        return $skills;
    }

    /**
     * @param $events
     *
     * @return Collection
     */
    private function CollectTalentInfo($events): Collection
    {
        $talents = new Collection();

        foreach ($events as $event) {
            if (!property_exists($event->data, 'Talents')) {
                continue;
            }

            foreach ($event->data->Talents as $talentId) {
                $talents->push($talentId);
            }
        }

        $talents = $talents->mapToGroups(function ($element) {
            return [$element => $element];
        })->map(function ($value, $key) {
            return [
                'talent' => Talent::find($key),
                'count'  => count($value),
            ];
        })->filter(function ($info) {
            return $info['talent'] !== null;
        })->sortByDesc(function ($info) {
            return $info['count'];
        });

        $talentIds = $talents->keys()->toArray();

        foreach (Talent::get() as $talent) {
            if (in_array($talent->id, $talentIds)) {
                continue;
            }

            $talents[$talent->id] = ['talent' => $talent, 'count' => 0];
        }

        return $talents;
    }

    /**
     * @param $events
     *
     * @return Collection
     */
    private function CollectRelicInfo($events): Collection
    {
        $relics = new Collection();

        foreach ($events as $event) {
            if (!property_exists($event->data, 'Relics')) {
                continue;
            }

            foreach ($event->data->Relics as $relicId) {
                $relics->push($relicId);
            }
        }

        $relics = $relics->mapToGroups(function ($element) {
            return [$element => $element];
        })->map(function ($value, $key) {
            return [
                'relic' => Relic::find($key),
                'count' => count($value),
            ];
        })->filter(function ($info) {
            return $info['relic'] !== null;
        })->sortByDesc(function ($info) {
            return $info['count'];
        });

        $relicIds = $relics->keys()->toArray();

        foreach (Relic::get() as $relic) {
            if (in_array($relic->id, $relicIds)) {
                continue;
            }

            $relics[$relic->id] = ['relic' => $relic, 'count' => 0];
        }

        return $relics;
    }
}
