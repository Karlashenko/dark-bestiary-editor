<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\BehaviourTree;
use App\Http\Requests\BehaviourTreeNodeRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BehaviourTreeController
 *
 * @package App\Http\Controllers\Frontend
 */
class BehaviourTreeController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $behaviourTrees = BehaviourTree::byType((string) $request->query('type'))
                                       ->orderBy('type')
                                       ->orderBy('name')
                                       ->get();

        return \response()->json($behaviourTrees->toArray());
    }

    /**
     * @param BehaviourTree $behaviourTree
     *
     * @return Response
     */
    public function show(BehaviourTree $behaviourTree): Response
    {
        return \response()->json($behaviourTree->toArray());
    }

    /**
     * @param BehaviourTreeNodeRequest $request
     *
     * @return Response
     * @throws \Throwable
     * @throws \Exception
     */
    public function store(Request $request): Response
    {
        $behaviourTree = BehaviourTree::populate(new BehaviourTree(), $request);

        return \response()->json($behaviourTree->toArray());
    }

    /**
     * @param Request       $request
     * @param BehaviourTree $behaviourTree
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, BehaviourTree $behaviourTree): Response
    {
        $behaviourTree = BehaviourTree::populate($behaviourTree, $request);

        return \response()->json($behaviourTree->toArray());
    }

    /**
     * @param BehaviourTree $behaviourTree
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(BehaviourTree $behaviourTree): Response
    {
        $behaviourTree->delete();

        return \response()->json((object) []);
    }
}
