<?php

namespace VendorDuplicator;

/*
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
/**
 * Service definition for Clouddebugger (v2).
 *
 * <p>
 * Lets you examine the stack and variables of your running application without
 * stopping or slowing it down.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://cloud.google.com/tools/cloud-debugger" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 * @internal
 */
class Google_Service_Clouddebugger extends Google_Service
{
    /** View and manage your data across Google Cloud Platform services. */
    const CLOUD_PLATFORM = "https://www.googleapis.com/auth/cloud-platform";
    /** Manage cloud debugger. */
    const CLOUD_DEBUGGER = "https://www.googleapis.com/auth/cloud_debugger";
    /** Manage active breakpoints in cloud debugger. */
    const CLOUD_DEBUGLETCONTROLLER = "https://www.googleapis.com/auth/cloud_debugletcontroller";
    public $controller_debuggees;
    public $controller_debuggees_breakpoints;
    public $debugger_debuggees;
    public $debugger_debuggees_breakpoints;
    /**
     * Constructs the internal representation of the Clouddebugger service.
     *
     * @param Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
        $this->rootUrl = 'https://clouddebugger.googleapis.com/';
        $this->servicePath = '';
        $this->version = 'v2';
        $this->serviceName = 'clouddebugger';
        $this->controller_debuggees = new Google_Service_Clouddebugger_ControllerDebuggees_Resource($this, $this->serviceName, 'debuggees', array('methods' => array('register' => array('path' => 'v2/controller/debuggees/register', 'httpMethod' => 'POST', 'parameters' => array()))));
        $this->controller_debuggees_breakpoints = new Google_Service_Clouddebugger_ControllerDebuggeesBreakpoints_Resource($this, $this->serviceName, 'breakpoints', array('methods' => array('list' => array('path' => 'v2/controller/debuggees/{debuggeeId}/breakpoints', 'httpMethod' => 'GET', 'parameters' => array('debuggeeId' => array('location' => 'path', 'type' => 'string', 'required' => \true), 'waitToken' => array('location' => 'query', 'type' => 'string'), 'successOnTimeout' => array('location' => 'query', 'type' => 'boolean'))), 'update' => array('path' => 'v2/controller/debuggees/{debuggeeId}/breakpoints/{id}', 'httpMethod' => 'PUT', 'parameters' => array('debuggeeId' => array('location' => 'path', 'type' => 'string', 'required' => \true), 'id' => array('location' => 'path', 'type' => 'string', 'required' => \true))))));
        $this->debugger_debuggees = new Google_Service_Clouddebugger_DebuggerDebuggees_Resource($this, $this->serviceName, 'debuggees', array('methods' => array('list' => array('path' => 'v2/debugger/debuggees', 'httpMethod' => 'GET', 'parameters' => array('project' => array('location' => 'query', 'type' => 'string'), 'includeInactive' => array('location' => 'query', 'type' => 'boolean'))))));
        $this->debugger_debuggees_breakpoints = new Google_Service_Clouddebugger_DebuggerDebuggeesBreakpoints_Resource($this, $this->serviceName, 'breakpoints', array('methods' => array('delete' => array('path' => 'v2/debugger/debuggees/{debuggeeId}/breakpoints/{breakpointId}', 'httpMethod' => 'DELETE', 'parameters' => array('debuggeeId' => array('location' => 'path', 'type' => 'string', 'required' => \true), 'breakpointId' => array('location' => 'path', 'type' => 'string', 'required' => \true))), 'get' => array('path' => 'v2/debugger/debuggees/{debuggeeId}/breakpoints/{breakpointId}', 'httpMethod' => 'GET', 'parameters' => array('debuggeeId' => array('location' => 'path', 'type' => 'string', 'required' => \true), 'breakpointId' => array('location' => 'path', 'type' => 'string', 'required' => \true))), 'list' => array('path' => 'v2/debugger/debuggees/{debuggeeId}/breakpoints', 'httpMethod' => 'GET', 'parameters' => array('debuggeeId' => array('location' => 'path', 'type' => 'string', 'required' => \true), 'includeAllUsers' => array('location' => 'query', 'type' => 'boolean'), 'includeInactive' => array('location' => 'query', 'type' => 'boolean'), 'action.value' => array('location' => 'query', 'type' => 'string'), 'stripResults' => array('location' => 'query', 'type' => 'boolean'), 'waitToken' => array('location' => 'query', 'type' => 'string'))), 'set' => array('path' => 'v2/debugger/debuggees/{debuggeeId}/breakpoints/set', 'httpMethod' => 'POST', 'parameters' => array('debuggeeId' => array('location' => 'path', 'type' => 'string', 'required' => \true))))));
    }
}
/*
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
/**
 * Service definition for Clouddebugger (v2).
 *
 * <p>
 * Lets you examine the stack and variables of your running application without
 * stopping or slowing it down.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://cloud.google.com/tools/cloud-debugger" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger', 'VendorDuplicator\\Google_Service_Clouddebugger', \false);
/**
 * The "controller" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $controller = $clouddebuggerService->controller;
 *  </code>
 * @internal
 */
class Google_Service_Clouddebugger_Controller_Resource extends Google_Service_Resource
{
}
/**
 * The "controller" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $controller = $clouddebuggerService->controller;
 *  </code>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_Controller_Resource', 'VendorDuplicator\\Google_Service_Clouddebugger_Controller_Resource', \false);
/**
 * The "debuggees" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $debuggees = $clouddebuggerService->debuggees;
 *  </code>
 * @internal
 */
class Google_Service_Clouddebugger_ControllerDebuggees_Resource extends Google_Service_Resource
{
    /**
     * Registers the debuggee with the controller service. All agents attached to
     * the same application should call this method with the same request content to
     * get back the same stable `debuggee_id`. Agents should call this method again
     * whenever `google.rpc.Code.NOT_FOUND` is returned from any controller method.
     * This allows the controller service to disable the agent or recover from any
     * data loss. If the debuggee is disabled by the server, the response will have
     * `is_disabled` set to `true`. (debuggees.register)
     *
     * @param Google_RegisterDebuggeeRequest $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Service_Clouddebugger_RegisterDebuggeeResponse
     */
    public function register(Google_Service_Clouddebugger_RegisterDebuggeeRequest $postBody, $optParams = array())
    {
        $params = array('postBody' => $postBody);
        $params = \array_merge($params, $optParams);
        return $this->call('register', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_RegisterDebuggeeResponse");
    }
}
/**
 * The "debuggees" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $debuggees = $clouddebuggerService->debuggees;
 *  </code>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ControllerDebuggees_Resource', 'VendorDuplicator\\Google_Service_Clouddebugger_ControllerDebuggees_Resource', \false);
/**
 * The "breakpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $breakpoints = $clouddebuggerService->breakpoints;
 *  </code>
 * @internal
 */
class Google_Service_Clouddebugger_ControllerDebuggeesBreakpoints_Resource extends Google_Service_Resource
{
    /**
     * Returns the list of all active breakpoints for the debuggee. The breakpoint
     * specification (location, condition, and expression fields) is semantically
     * immutable, although the field values may change. For example, an agent may
     * update the location line number to reflect the actual line where the
     * breakpoint was set, but this doesn't change the breakpoint semantics. This
     * means that an agent does not need to check if a breakpoint has changed when
     * it encounters the same breakpoint on a successive call. Moreover, an agent
     * should remember the breakpoints that are completed until the controller
     * removes them from the active list to avoid setting those breakpoints again.
     * (breakpoints.listControllerDebuggeesBreakpoints)
     *
     * @param string $debuggeeId Identifies the debuggee.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string waitToken A wait token that, if specified, blocks the
     * method call until the list of active breakpoints has changed, or a server
     * selected timeout has expired. The value should be set from the last returned
     * response.
     * @opt_param bool successOnTimeout If set to `true`, returns
     * `google.rpc.Code.OK` status and sets the `wait_expired` response field to
     * `true` when the server-selected timeout has expired (recommended). If set to
     * `false`, returns `google.rpc.Code.ABORTED` status when the server-selected
     * timeout has expired (deprecated).
     * @return Google_Service_Clouddebugger_ListActiveBreakpointsResponse
     */
    public function listControllerDebuggeesBreakpoints($debuggeeId, $optParams = array())
    {
        $params = array('debuggeeId' => $debuggeeId);
        $params = \array_merge($params, $optParams);
        return $this->call('list', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_ListActiveBreakpointsResponse");
    }
    /**
     * Updates the breakpoint state or mutable fields. The entire Breakpoint message
     * must be sent back to the controller service. Updates to active breakpoint
     * fields are only allowed if the new value does not change the breakpoint
     * specification. Updates to the `location`, `condition` and `expression` fields
     * should not alter the breakpoint semantics. These may only make changes such
     * as canonicalizing a value or snapping the location to the correct line of
     * code. (breakpoints.update)
     *
     * @param string $debuggeeId Identifies the debuggee being debugged.
     * @param string $id Breakpoint identifier, unique in the scope of the debuggee.
     * @param Google_UpdateActiveBreakpointRequest $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Service_Clouddebugger_UpdateActiveBreakpointResponse
     */
    public function update($debuggeeId, $id, Google_Service_Clouddebugger_UpdateActiveBreakpointRequest $postBody, $optParams = array())
    {
        $params = array('debuggeeId' => $debuggeeId, 'id' => $id, 'postBody' => $postBody);
        $params = \array_merge($params, $optParams);
        return $this->call('update', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_UpdateActiveBreakpointResponse");
    }
}
/**
 * The "breakpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $breakpoints = $clouddebuggerService->breakpoints;
 *  </code>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ControllerDebuggeesBreakpoints_Resource', 'VendorDuplicator\\Google_Service_Clouddebugger_ControllerDebuggeesBreakpoints_Resource', \false);
/**
 * The "debugger" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $debugger = $clouddebuggerService->debugger;
 *  </code>
 * @internal
 */
class Google_Service_Clouddebugger_Debugger_Resource extends Google_Service_Resource
{
}
/**
 * The "debugger" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $debugger = $clouddebuggerService->debugger;
 *  </code>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_Debugger_Resource', 'VendorDuplicator\\Google_Service_Clouddebugger_Debugger_Resource', \false);
/**
 * The "debuggees" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $debuggees = $clouddebuggerService->debuggees;
 *  </code>
 * @internal
 */
class Google_Service_Clouddebugger_DebuggerDebuggees_Resource extends Google_Service_Resource
{
    /**
     * Lists all the debuggees that the user can set breakpoints to.
     * (debuggees.listDebuggerDebuggees)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string project Project number of a Google Cloud project whose
     * debuggees to list.
     * @opt_param bool includeInactive When set to `true`, the result includes all
     * debuggees. Otherwise, the result includes only debuggees that are active.
     * @return Google_Service_Clouddebugger_ListDebuggeesResponse
     */
    public function listDebuggerDebuggees($optParams = array())
    {
        $params = array();
        $params = \array_merge($params, $optParams);
        return $this->call('list', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_ListDebuggeesResponse");
    }
}
/**
 * The "debuggees" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $debuggees = $clouddebuggerService->debuggees;
 *  </code>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_DebuggerDebuggees_Resource', 'VendorDuplicator\\Google_Service_Clouddebugger_DebuggerDebuggees_Resource', \false);
/**
 * The "breakpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $breakpoints = $clouddebuggerService->breakpoints;
 *  </code>
 * @internal
 */
class Google_Service_Clouddebugger_DebuggerDebuggeesBreakpoints_Resource extends Google_Service_Resource
{
    /**
     * Deletes the breakpoint from the debuggee. (breakpoints.delete)
     *
     * @param string $debuggeeId ID of the debuggee whose breakpoint to delete.
     * @param string $breakpointId ID of the breakpoint to delete.
     * @param array $optParams Optional parameters.
     * @return Google_Service_Clouddebugger_Empty
     */
    public function delete($debuggeeId, $breakpointId, $optParams = array())
    {
        $params = array('debuggeeId' => $debuggeeId, 'breakpointId' => $breakpointId);
        $params = \array_merge($params, $optParams);
        return $this->call('delete', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_Empty");
    }
    /**
     * Gets breakpoint information. (breakpoints.get)
     *
     * @param string $debuggeeId ID of the debuggee whose breakpoint to get.
     * @param string $breakpointId ID of the breakpoint to get.
     * @param array $optParams Optional parameters.
     * @return Google_Service_Clouddebugger_GetBreakpointResponse
     */
    public function get($debuggeeId, $breakpointId, $optParams = array())
    {
        $params = array('debuggeeId' => $debuggeeId, 'breakpointId' => $breakpointId);
        $params = \array_merge($params, $optParams);
        return $this->call('get', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_GetBreakpointResponse");
    }
    /**
     * Lists all breakpoints for the debuggee.
     * (breakpoints.listDebuggerDebuggeesBreakpoints)
     *
     * @param string $debuggeeId ID of the debuggee whose breakpoints to list.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeAllUsers When set to `true`, the response includes the
     * list of breakpoints set by any user. Otherwise, it includes only breakpoints
     * set by the caller.
     * @opt_param bool includeInactive When set to `true`, the response includes
     * active and inactive breakpoints. Otherwise, it includes only active
     * breakpoints.
     * @opt_param string action.value Only breakpoints with the specified action
     * will pass the filter.
     * @opt_param bool stripResults When set to `true`, the response breakpoints are
     * stripped of the results fields: `stack_frames`, `evaluated_expressions` and
     * `variable_table`.
     * @opt_param string waitToken A wait token that, if specified, blocks the call
     * until the breakpoints list has changed, or a server selected timeout has
     * expired. The value should be set from the last response. The error code
     * `google.rpc.Code.ABORTED` (RPC) is returned on wait timeout, which should be
     * called again with the same `wait_token`.
     * @return Google_Service_Clouddebugger_ListBreakpointsResponse
     */
    public function listDebuggerDebuggeesBreakpoints($debuggeeId, $optParams = array())
    {
        $params = array('debuggeeId' => $debuggeeId);
        $params = \array_merge($params, $optParams);
        return $this->call('list', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_ListBreakpointsResponse");
    }
    /**
     * Sets the breakpoint to the debuggee. (breakpoints.set)
     *
     * @param string $debuggeeId ID of the debuggee where the breakpoint is to be
     * set.
     * @param Google_Breakpoint $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Service_Clouddebugger_SetBreakpointResponse
     */
    public function set($debuggeeId, Google_Service_Clouddebugger_Breakpoint $postBody, $optParams = array())
    {
        $params = array('debuggeeId' => $debuggeeId, 'postBody' => $postBody);
        $params = \array_merge($params, $optParams);
        return $this->call('set', array($params), "VendorDuplicator\\Google_Service_Clouddebugger_SetBreakpointResponse");
    }
}
/**
 * The "breakpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddebuggerService = new Google_Service_Clouddebugger(...);
 *   $breakpoints = $clouddebuggerService->breakpoints;
 *  </code>
 * @internal
 */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_DebuggerDebuggeesBreakpoints_Resource', 'VendorDuplicator\\Google_Service_Clouddebugger_DebuggerDebuggeesBreakpoints_Resource', \false);
/** @internal */
class Google_Service_Clouddebugger_AliasContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    public $kind;
    public $name;
    public function setKind($kind)
    {
        $this->kind = $kind;
    }
    public function getKind()
    {
        return $this->kind;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_AliasContext', 'VendorDuplicator\\Google_Service_Clouddebugger_AliasContext', \false);
/** @internal */
class Google_Service_Clouddebugger_Breakpoint extends Google_Collection
{
    protected $collection_key = 'variableTable';
    protected $internal_gapi_mappings = array();
    public $action;
    public $condition;
    public $createTime;
    protected $evaluatedExpressionsType = 'VendorDuplicator\\Google_Service_Clouddebugger_Variable';
    protected $evaluatedExpressionsDataType = 'array';
    public $expressions;
    public $finalTime;
    public $id;
    public $isFinalState;
    protected $locationType = 'VendorDuplicator\\Google_Service_Clouddebugger_SourceLocation';
    protected $locationDataType = '';
    public $logLevel;
    public $logMessageFormat;
    protected $stackFramesType = 'VendorDuplicator\\Google_Service_Clouddebugger_StackFrame';
    protected $stackFramesDataType = 'array';
    protected $statusType = 'VendorDuplicator\\Google_Service_Clouddebugger_StatusMessage';
    protected $statusDataType = '';
    public $userEmail;
    protected $variableTableType = 'VendorDuplicator\\Google_Service_Clouddebugger_Variable';
    protected $variableTableDataType = 'array';
    public function setAction($action)
    {
        $this->action = $action;
    }
    public function getAction()
    {
        return $this->action;
    }
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }
    public function getCondition()
    {
        return $this->condition;
    }
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }
    public function getCreateTime()
    {
        return $this->createTime;
    }
    public function setEvaluatedExpressions($evaluatedExpressions)
    {
        $this->evaluatedExpressions = $evaluatedExpressions;
    }
    public function getEvaluatedExpressions()
    {
        return $this->evaluatedExpressions;
    }
    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;
    }
    public function getExpressions()
    {
        return $this->expressions;
    }
    public function setFinalTime($finalTime)
    {
        $this->finalTime = $finalTime;
    }
    public function getFinalTime()
    {
        return $this->finalTime;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setIsFinalState($isFinalState)
    {
        $this->isFinalState = $isFinalState;
    }
    public function getIsFinalState()
    {
        return $this->isFinalState;
    }
    public function setLocation(Google_Service_Clouddebugger_SourceLocation $location)
    {
        $this->location = $location;
    }
    public function getLocation()
    {
        return $this->location;
    }
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;
    }
    public function getLogLevel()
    {
        return $this->logLevel;
    }
    public function setLogMessageFormat($logMessageFormat)
    {
        $this->logMessageFormat = $logMessageFormat;
    }
    public function getLogMessageFormat()
    {
        return $this->logMessageFormat;
    }
    public function setStackFrames($stackFrames)
    {
        $this->stackFrames = $stackFrames;
    }
    public function getStackFrames()
    {
        return $this->stackFrames;
    }
    public function setStatus(Google_Service_Clouddebugger_StatusMessage $status)
    {
        $this->status = $status;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }
    public function getUserEmail()
    {
        return $this->userEmail;
    }
    public function setVariableTable($variableTable)
    {
        $this->variableTable = $variableTable;
    }
    public function getVariableTable()
    {
        return $this->variableTable;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint', 'VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint', \false);
/** @internal */
class Google_Service_Clouddebugger_CloudRepoSourceContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $aliasContextType = 'VendorDuplicator\\Google_Service_Clouddebugger_AliasContext';
    protected $aliasContextDataType = '';
    public $aliasName;
    protected $repoIdType = 'VendorDuplicator\\Google_Service_Clouddebugger_RepoId';
    protected $repoIdDataType = '';
    public $revisionId;
    public function setAliasContext(Google_Service_Clouddebugger_AliasContext $aliasContext)
    {
        $this->aliasContext = $aliasContext;
    }
    public function getAliasContext()
    {
        return $this->aliasContext;
    }
    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;
    }
    public function getAliasName()
    {
        return $this->aliasName;
    }
    public function setRepoId(Google_Service_Clouddebugger_RepoId $repoId)
    {
        $this->repoId = $repoId;
    }
    public function getRepoId()
    {
        return $this->repoId;
    }
    public function setRevisionId($revisionId)
    {
        $this->revisionId = $revisionId;
    }
    public function getRevisionId()
    {
        return $this->revisionId;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_CloudRepoSourceContext', 'VendorDuplicator\\Google_Service_Clouddebugger_CloudRepoSourceContext', \false);
/** @internal */
class Google_Service_Clouddebugger_CloudWorkspaceId extends Google_Model
{
    protected $internal_gapi_mappings = array();
    public $name;
    protected $repoIdType = 'VendorDuplicator\\Google_Service_Clouddebugger_RepoId';
    protected $repoIdDataType = '';
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setRepoId(Google_Service_Clouddebugger_RepoId $repoId)
    {
        $this->repoId = $repoId;
    }
    public function getRepoId()
    {
        return $this->repoId;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_CloudWorkspaceId', 'VendorDuplicator\\Google_Service_Clouddebugger_CloudWorkspaceId', \false);
/** @internal */
class Google_Service_Clouddebugger_CloudWorkspaceSourceContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    public $snapshotId;
    protected $workspaceIdType = 'VendorDuplicator\\Google_Service_Clouddebugger_CloudWorkspaceId';
    protected $workspaceIdDataType = '';
    public function setSnapshotId($snapshotId)
    {
        $this->snapshotId = $snapshotId;
    }
    public function getSnapshotId()
    {
        return $this->snapshotId;
    }
    public function setWorkspaceId(Google_Service_Clouddebugger_CloudWorkspaceId $workspaceId)
    {
        $this->workspaceId = $workspaceId;
    }
    public function getWorkspaceId()
    {
        return $this->workspaceId;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_CloudWorkspaceSourceContext', 'VendorDuplicator\\Google_Service_Clouddebugger_CloudWorkspaceSourceContext', \false);
/** @internal */
class Google_Service_Clouddebugger_Debuggee extends Google_Collection
{
    protected $collection_key = 'sourceContexts';
    protected $internal_gapi_mappings = array();
    public $agentVersion;
    public $description;
    protected $extSourceContextsType = 'VendorDuplicator\\Google_Service_Clouddebugger_ExtendedSourceContext';
    protected $extSourceContextsDataType = 'array';
    public $id;
    public $isDisabled;
    public $isInactive;
    public $labels;
    public $project;
    protected $sourceContextsType = 'VendorDuplicator\\Google_Service_Clouddebugger_SourceContext';
    protected $sourceContextsDataType = 'array';
    protected $statusType = 'VendorDuplicator\\Google_Service_Clouddebugger_StatusMessage';
    protected $statusDataType = '';
    public $uniquifier;
    public function setAgentVersion($agentVersion)
    {
        $this->agentVersion = $agentVersion;
    }
    public function getAgentVersion()
    {
        return $this->agentVersion;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setExtSourceContexts($extSourceContexts)
    {
        $this->extSourceContexts = $extSourceContexts;
    }
    public function getExtSourceContexts()
    {
        return $this->extSourceContexts;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setIsDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;
    }
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }
    public function setIsInactive($isInactive)
    {
        $this->isInactive = $isInactive;
    }
    public function getIsInactive()
    {
        return $this->isInactive;
    }
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }
    public function getLabels()
    {
        return $this->labels;
    }
    public function setProject($project)
    {
        $this->project = $project;
    }
    public function getProject()
    {
        return $this->project;
    }
    public function setSourceContexts($sourceContexts)
    {
        $this->sourceContexts = $sourceContexts;
    }
    public function getSourceContexts()
    {
        return $this->sourceContexts;
    }
    public function setStatus(Google_Service_Clouddebugger_StatusMessage $status)
    {
        $this->status = $status;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setUniquifier($uniquifier)
    {
        $this->uniquifier = $uniquifier;
    }
    public function getUniquifier()
    {
        return $this->uniquifier;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_Debuggee', 'VendorDuplicator\\Google_Service_Clouddebugger_Debuggee', \false);
/** @internal */
class Google_Service_Clouddebugger_Empty extends Google_Model
{
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_Empty', 'VendorDuplicator\\Google_Service_Clouddebugger_Empty', \false);
/** @internal */
class Google_Service_Clouddebugger_ExtendedSourceContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $contextType = 'VendorDuplicator\\Google_Service_Clouddebugger_SourceContext';
    protected $contextDataType = '';
    public $labels;
    public function setContext(Google_Service_Clouddebugger_SourceContext $context)
    {
        $this->context = $context;
    }
    public function getContext()
    {
        return $this->context;
    }
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }
    public function getLabels()
    {
        return $this->labels;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ExtendedSourceContext', 'VendorDuplicator\\Google_Service_Clouddebugger_ExtendedSourceContext', \false);
/** @internal */
class Google_Service_Clouddebugger_FormatMessage extends Google_Collection
{
    protected $collection_key = 'parameters';
    protected $internal_gapi_mappings = array();
    public $format;
    public $parameters;
    public function setFormat($format)
    {
        $this->format = $format;
    }
    public function getFormat()
    {
        return $this->format;
    }
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
    public function getParameters()
    {
        return $this->parameters;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_FormatMessage', 'VendorDuplicator\\Google_Service_Clouddebugger_FormatMessage', \false);
/** @internal */
class Google_Service_Clouddebugger_GerritSourceContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $aliasContextType = 'VendorDuplicator\\Google_Service_Clouddebugger_AliasContext';
    protected $aliasContextDataType = '';
    public $aliasName;
    public $gerritProject;
    public $hostUri;
    public $revisionId;
    public function setAliasContext(Google_Service_Clouddebugger_AliasContext $aliasContext)
    {
        $this->aliasContext = $aliasContext;
    }
    public function getAliasContext()
    {
        return $this->aliasContext;
    }
    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;
    }
    public function getAliasName()
    {
        return $this->aliasName;
    }
    public function setGerritProject($gerritProject)
    {
        $this->gerritProject = $gerritProject;
    }
    public function getGerritProject()
    {
        return $this->gerritProject;
    }
    public function setHostUri($hostUri)
    {
        $this->hostUri = $hostUri;
    }
    public function getHostUri()
    {
        return $this->hostUri;
    }
    public function setRevisionId($revisionId)
    {
        $this->revisionId = $revisionId;
    }
    public function getRevisionId()
    {
        return $this->revisionId;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_GerritSourceContext', 'VendorDuplicator\\Google_Service_Clouddebugger_GerritSourceContext', \false);
/** @internal */
class Google_Service_Clouddebugger_GetBreakpointResponse extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $breakpointType = 'VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint';
    protected $breakpointDataType = '';
    public function setBreakpoint(Google_Service_Clouddebugger_Breakpoint $breakpoint)
    {
        $this->breakpoint = $breakpoint;
    }
    public function getBreakpoint()
    {
        return $this->breakpoint;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_GetBreakpointResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_GetBreakpointResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_GitSourceContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    public $revisionId;
    public $url;
    public function setRevisionId($revisionId)
    {
        $this->revisionId = $revisionId;
    }
    public function getRevisionId()
    {
        return $this->revisionId;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }
    public function getUrl()
    {
        return $this->url;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_GitSourceContext', 'VendorDuplicator\\Google_Service_Clouddebugger_GitSourceContext', \false);
/** @internal */
class Google_Service_Clouddebugger_ListActiveBreakpointsResponse extends Google_Collection
{
    protected $collection_key = 'breakpoints';
    protected $internal_gapi_mappings = array();
    protected $breakpointsType = 'VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint';
    protected $breakpointsDataType = 'array';
    public $nextWaitToken;
    public $waitExpired;
    public function setBreakpoints($breakpoints)
    {
        $this->breakpoints = $breakpoints;
    }
    public function getBreakpoints()
    {
        return $this->breakpoints;
    }
    public function setNextWaitToken($nextWaitToken)
    {
        $this->nextWaitToken = $nextWaitToken;
    }
    public function getNextWaitToken()
    {
        return $this->nextWaitToken;
    }
    public function setWaitExpired($waitExpired)
    {
        $this->waitExpired = $waitExpired;
    }
    public function getWaitExpired()
    {
        return $this->waitExpired;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ListActiveBreakpointsResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_ListActiveBreakpointsResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_ListBreakpointsResponse extends Google_Collection
{
    protected $collection_key = 'breakpoints';
    protected $internal_gapi_mappings = array();
    protected $breakpointsType = 'VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint';
    protected $breakpointsDataType = 'array';
    public $nextWaitToken;
    public function setBreakpoints($breakpoints)
    {
        $this->breakpoints = $breakpoints;
    }
    public function getBreakpoints()
    {
        return $this->breakpoints;
    }
    public function setNextWaitToken($nextWaitToken)
    {
        $this->nextWaitToken = $nextWaitToken;
    }
    public function getNextWaitToken()
    {
        return $this->nextWaitToken;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ListBreakpointsResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_ListBreakpointsResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_ListDebuggeesResponse extends Google_Collection
{
    protected $collection_key = 'debuggees';
    protected $internal_gapi_mappings = array();
    protected $debuggeesType = 'VendorDuplicator\\Google_Service_Clouddebugger_Debuggee';
    protected $debuggeesDataType = 'array';
    public function setDebuggees($debuggees)
    {
        $this->debuggees = $debuggees;
    }
    public function getDebuggees()
    {
        return $this->debuggees;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ListDebuggeesResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_ListDebuggeesResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_ProjectRepoId extends Google_Model
{
    protected $internal_gapi_mappings = array();
    public $projectId;
    public $repoName;
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }
    public function getProjectId()
    {
        return $this->projectId;
    }
    public function setRepoName($repoName)
    {
        $this->repoName = $repoName;
    }
    public function getRepoName()
    {
        return $this->repoName;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_ProjectRepoId', 'VendorDuplicator\\Google_Service_Clouddebugger_ProjectRepoId', \false);
/** @internal */
class Google_Service_Clouddebugger_RegisterDebuggeeRequest extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $debuggeeType = 'VendorDuplicator\\Google_Service_Clouddebugger_Debuggee';
    protected $debuggeeDataType = '';
    public function setDebuggee(Google_Service_Clouddebugger_Debuggee $debuggee)
    {
        $this->debuggee = $debuggee;
    }
    public function getDebuggee()
    {
        return $this->debuggee;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_RegisterDebuggeeRequest', 'VendorDuplicator\\Google_Service_Clouddebugger_RegisterDebuggeeRequest', \false);
/** @internal */
class Google_Service_Clouddebugger_RegisterDebuggeeResponse extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $debuggeeType = 'VendorDuplicator\\Google_Service_Clouddebugger_Debuggee';
    protected $debuggeeDataType = '';
    public function setDebuggee(Google_Service_Clouddebugger_Debuggee $debuggee)
    {
        $this->debuggee = $debuggee;
    }
    public function getDebuggee()
    {
        return $this->debuggee;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_RegisterDebuggeeResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_RegisterDebuggeeResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_RepoId extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $projectRepoIdType = 'VendorDuplicator\\Google_Service_Clouddebugger_ProjectRepoId';
    protected $projectRepoIdDataType = '';
    public $uid;
    public function setProjectRepoId(Google_Service_Clouddebugger_ProjectRepoId $projectRepoId)
    {
        $this->projectRepoId = $projectRepoId;
    }
    public function getProjectRepoId()
    {
        return $this->projectRepoId;
    }
    public function setUid($uid)
    {
        $this->uid = $uid;
    }
    public function getUid()
    {
        return $this->uid;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_RepoId', 'VendorDuplicator\\Google_Service_Clouddebugger_RepoId', \false);
/** @internal */
class Google_Service_Clouddebugger_SetBreakpointResponse extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $breakpointType = 'VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint';
    protected $breakpointDataType = '';
    public function setBreakpoint(Google_Service_Clouddebugger_Breakpoint $breakpoint)
    {
        $this->breakpoint = $breakpoint;
    }
    public function getBreakpoint()
    {
        return $this->breakpoint;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_SetBreakpointResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_SetBreakpointResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_SourceContext extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $cloudRepoType = 'VendorDuplicator\\Google_Service_Clouddebugger_CloudRepoSourceContext';
    protected $cloudRepoDataType = '';
    protected $cloudWorkspaceType = 'VendorDuplicator\\Google_Service_Clouddebugger_CloudWorkspaceSourceContext';
    protected $cloudWorkspaceDataType = '';
    protected $gerritType = 'VendorDuplicator\\Google_Service_Clouddebugger_GerritSourceContext';
    protected $gerritDataType = '';
    protected $gitType = 'VendorDuplicator\\Google_Service_Clouddebugger_GitSourceContext';
    protected $gitDataType = '';
    public function setCloudRepo(Google_Service_Clouddebugger_CloudRepoSourceContext $cloudRepo)
    {
        $this->cloudRepo = $cloudRepo;
    }
    public function getCloudRepo()
    {
        return $this->cloudRepo;
    }
    public function setCloudWorkspace(Google_Service_Clouddebugger_CloudWorkspaceSourceContext $cloudWorkspace)
    {
        $this->cloudWorkspace = $cloudWorkspace;
    }
    public function getCloudWorkspace()
    {
        return $this->cloudWorkspace;
    }
    public function setGerrit(Google_Service_Clouddebugger_GerritSourceContext $gerrit)
    {
        $this->gerrit = $gerrit;
    }
    public function getGerrit()
    {
        return $this->gerrit;
    }
    public function setGit(Google_Service_Clouddebugger_GitSourceContext $git)
    {
        $this->git = $git;
    }
    public function getGit()
    {
        return $this->git;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_SourceContext', 'VendorDuplicator\\Google_Service_Clouddebugger_SourceContext', \false);
/** @internal */
class Google_Service_Clouddebugger_SourceLocation extends Google_Model
{
    protected $internal_gapi_mappings = array();
    public $line;
    public $path;
    public function setLine($line)
    {
        $this->line = $line;
    }
    public function getLine()
    {
        return $this->line;
    }
    public function setPath($path)
    {
        $this->path = $path;
    }
    public function getPath()
    {
        return $this->path;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_SourceLocation', 'VendorDuplicator\\Google_Service_Clouddebugger_SourceLocation', \false);
/** @internal */
class Google_Service_Clouddebugger_StackFrame extends Google_Collection
{
    protected $collection_key = 'locals';
    protected $internal_gapi_mappings = array();
    protected $argumentsType = 'VendorDuplicator\\Google_Service_Clouddebugger_Variable';
    protected $argumentsDataType = 'array';
    public $function;
    protected $localsType = 'VendorDuplicator\\Google_Service_Clouddebugger_Variable';
    protected $localsDataType = 'array';
    protected $locationType = 'VendorDuplicator\\Google_Service_Clouddebugger_SourceLocation';
    protected $locationDataType = '';
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }
    public function getArguments()
    {
        return $this->arguments;
    }
    public function setFunction($function)
    {
        $this->function = $function;
    }
    public function getFunction()
    {
        return $this->function;
    }
    public function setLocals($locals)
    {
        $this->locals = $locals;
    }
    public function getLocals()
    {
        return $this->locals;
    }
    public function setLocation(Google_Service_Clouddebugger_SourceLocation $location)
    {
        $this->location = $location;
    }
    public function getLocation()
    {
        return $this->location;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_StackFrame', 'VendorDuplicator\\Google_Service_Clouddebugger_StackFrame', \false);
/** @internal */
class Google_Service_Clouddebugger_StatusMessage extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $descriptionType = 'VendorDuplicator\\Google_Service_Clouddebugger_FormatMessage';
    protected $descriptionDataType = '';
    public $isError;
    public $refersTo;
    public function setDescription(Google_Service_Clouddebugger_FormatMessage $description)
    {
        $this->description = $description;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setIsError($isError)
    {
        $this->isError = $isError;
    }
    public function getIsError()
    {
        return $this->isError;
    }
    public function setRefersTo($refersTo)
    {
        $this->refersTo = $refersTo;
    }
    public function getRefersTo()
    {
        return $this->refersTo;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_StatusMessage', 'VendorDuplicator\\Google_Service_Clouddebugger_StatusMessage', \false);
/** @internal */
class Google_Service_Clouddebugger_UpdateActiveBreakpointRequest extends Google_Model
{
    protected $internal_gapi_mappings = array();
    protected $breakpointType = 'VendorDuplicator\\Google_Service_Clouddebugger_Breakpoint';
    protected $breakpointDataType = '';
    public function setBreakpoint(Google_Service_Clouddebugger_Breakpoint $breakpoint)
    {
        $this->breakpoint = $breakpoint;
    }
    public function getBreakpoint()
    {
        return $this->breakpoint;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_UpdateActiveBreakpointRequest', 'VendorDuplicator\\Google_Service_Clouddebugger_UpdateActiveBreakpointRequest', \false);
/** @internal */
class Google_Service_Clouddebugger_UpdateActiveBreakpointResponse extends Google_Model
{
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_UpdateActiveBreakpointResponse', 'VendorDuplicator\\Google_Service_Clouddebugger_UpdateActiveBreakpointResponse', \false);
/** @internal */
class Google_Service_Clouddebugger_Variable extends Google_Collection
{
    protected $collection_key = 'members';
    protected $internal_gapi_mappings = array();
    protected $membersType = 'VendorDuplicator\\Google_Service_Clouddebugger_Variable';
    protected $membersDataType = 'array';
    public $name;
    protected $statusType = 'VendorDuplicator\\Google_Service_Clouddebugger_StatusMessage';
    protected $statusDataType = '';
    public $type;
    public $value;
    public $varTableIndex;
    public function setMembers($members)
    {
        $this->members = $members;
    }
    public function getMembers()
    {
        return $this->members;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setStatus(Google_Service_Clouddebugger_StatusMessage $status)
    {
        $this->status = $status;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function setVarTableIndex($varTableIndex)
    {
        $this->varTableIndex = $varTableIndex;
    }
    public function getVarTableIndex()
    {
        return $this->varTableIndex;
    }
}
/** @internal */
\class_alias('VendorDuplicator\\Google_Service_Clouddebugger_Variable', 'VendorDuplicator\\Google_Service_Clouddebugger_Variable', \false);
