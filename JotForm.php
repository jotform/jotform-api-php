<?php
/**
 * JotForm API - PHP Client
 *
 * @copyright   2013 Interlogy, LLC.
 * @link        http://www.jotform.com
 * @version     1.0
 * @package     JotFormAPI
 */

class JotForm {
    public $baseURL = 'https://api.jotform.com';
    private $apiKey;
    private $debugMode;
    private $outputType;
    private $apiVersion = 'v1';

    public function __construct($apiKey = '', $outputType = 'json', $debugMode = false) {

        $this->apiKey = $apiKey;
        $this->debugMode = $debugMode;
        $this->outputType = strtolower($outputType);

    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    private function _debugLog($str) {
        if ($this->debugMode){
            print_r(PHP_EOL);
            print_r($str);
        }
    }

    private function _debugDump($obj) {
        if ($this->debugMode){
            print_r(PHP_EOL);
            var_dump($obj);
        }
    }

    private function _executeHttpRequest($path, $params = array(), $method) {
        if ($this->outputType != 'json') {
            $path = "{$path}.xml";
        }

        $url = implode('/', array($this->baseURL, $this->apiVersion, $path));

        $this->_debugDump($params);

        if ($method == 'GET' && $params != null){
            $params_array = array();
            foreach ($params as $key => $value) {
                $params_array[] = "{$key}={$value}";
            }
            $params_string = '?' . implode('&', $params_array);
            unset($params_array);
            $url = $url.$params_string;
            $this->_debugLog('params string: '.$params_string);
        }

        $this->_debugLog('fetching url: '.$url);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JOTFORM_PHP_WRAPPER');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('APIKEY: '.$this->apiKey));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        switch ($method) {
            case 'POST':
                $this->_debugLog('posting');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            break;
            case 'DELETE':
                $this->_debugLog('delete');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
        }

        $result = curl_exec($ch);

        if ($result == false) {
            throw new Exception(curl_error($ch), 400);
        }

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_debugLog('http code is: '.$http_status);

        if ($this->outputType == 'json') {
            $result_obj = json_decode($result, true);
        } else {
            $result_obj = utf8_decode($result);
        }

        if ($http_status != 200) {

            switch ($http_status) {
                case 400:
                case 403:
                case 404:
                    throw new JotFormException($result_obj["message"], $http_status );
                break;
                case 401:
                    throw new JotFormException("Invalid API key or Unauthorized API call", $http_status);
                break;
                case 503:
                    throw new JotFormException("Service is unavailable, rate limits etc exceeded!", $http_status);
                break;

                default:
                    throw new JotFormException($result_obj["info"], $http_status);
                break;
            }
        }

        curl_close($ch);

        if ($this->outputType == 'json') {
            if (isset($result_obj['content'])) {
                return $result_obj['content'];
            } else {
                return $result_obj;
            }
        } else {
            return $result_obj;
        }
    }

    private function _executeGetRequest($url, $params = array()) {
        return $this->_executeHttpRequest($url, $params, 'GET');
    }

    private function _executePostRequest($url, $params) {
        return $this->_executeHttpRequest($url, $params, 'POST');
    }

    private function _executePutRequest($url, $params) {
        return $this->_executeHttpRequest($url, $params, 'PUT');
    }

    private function _executeDeleteRequest($url, $params = array()) {
        return $this->_executeHttpRequest($url, $params, 'DELETE');
    }

    private function createConditions($offset, $limit, $filter, $orderby) {
        $params = array();
        foreach (array('offset', 'limit', 'filter', 'orderby') as $arg) {
             if (${$arg}) {
                 $params[strtolower($arg)] = ${$arg};
                 if ($arg == "filter") {
                     $params[$arg] = json_encode($params[$arg]);
                 }
             }
        }
        return $params;
    }

    private function createHistoryQuery($action, $date, $sortBy, $startDate, $endDate) {
        foreach (array('action', 'date', 'sortBy', 'startDate', 'endDate') as $arg) {
             if (${$arg}) {
                 $params[$arg] = ${$arg};
             }
        }
        return $params;
    }

    /**
     * [getUser Get user account details for a JotForm user]
     * @return [array] [Returns user account type, avatar URL, name, email, website URL and account limits.]
     */
    public function getUser() {
        $res = $this->_executeGetRequest('user');
        return $res;
    }

    /**
    * [getUserUsage Get number of form submissions received this month]
    * @return [array] [Returns number of submissions, number of SSL form submissions, payment form submissions and upload space used by user.]
    */
    public function getUsage(){
        return $this->_executeGetRequest('user/usage');
    }

    /**
     * [getForms Get a list of forms for this account]
     * @param [integer] $offset [Start of each result set for form list. (optional)]
     * @param [integer] $limit [Number of results in each result set for form list. (optional)]
     * @param [array] $filter [Filters the query results to fetch a specific form range.(optional)]
     * @param [string] $orderBy [Order results by a form field name. (optional)]
     * @return [array] [Returns basic details such as title of the form, when it was created, number of new and total submissions.]
     */
    public function getForms($offset = 0, $limit = 0, $filter = null, $orderby = null) {
        $params = $this->createConditions($offset, $limit, $filter, $orderby);
        return $this->_executeGetRequest('user/forms', $params);
    }

    /**
     * [getSubmissions Get a list of submissions for this account]
     * @param [int] $offset [Start of each result set for form list. (optional)]
     * @param [int] $limit [Number of results in each result set for form list. (optional)]
     * @param [array] $filter [Filters the query results to fetch a specific form range.(optional)]
     * @param [string] $orderBy [Order results by a form field name. (optional)]
     * @return [array] [Returns basic details such as title of the form, when it was created, number of new and total submissions.]
     */
    public function getSubmissions($offset = 0, $limit = 0, $filter = null, $orderby = null) {
        $params = $this->createConditions($offset, $limit, $filter, $orderby);
        return $this->_executeGetRequest('user/submissions', $params);
    }

    /**
    * [getUserSubusers Get a list of sub users for this account]
    * @return [array] [Returns list of forms and form folders with access privileges.]
    */
    public function getSubusers() {
        return $this->_executeGetRequest('user/subusers');
    }

    /**
    * [getUserFolders Get a list of form folders for this account]
    * @return [array] [Returns name of the folder and owner of the folder for shared folders.]
    */
    public function getFolders() {
        return $this->_executeGetRequest('user/folders');
    }

    /**
    * [getReports List of URLS for reports in this account]
    * @return [array] [Returns reports for all of the forms. ie. Excel, CSV, printable charts, embeddable HTML tables.]
    */
    public function getReports() {
        return $this->_executeGetRequest('user/reports');
    }

    /**
    * [getSettings Get user's settings for this account]
    * @return [array]  [Returns user's time zone and language.]
    */
    public function getSettings() {
        return $this->_executeGetRequest('user/settings');
    }

    /**
    * [updateSettings Update user's settings]
    * @param [array] $settings [New user setting values with setting keys]
    * @return [array] [Returns changes on user settings]
    */
    public function updateSettings($settings) {
        return $this->_executePostRequest('user/settings', $settings);
    }

    /**
    * [getHistory Get user activity log]
    * @param [enum] $action [Filter results by activity performed. Default is 'all'.]
    * @param [enum] $date [Limit results by a date range. If you'd like to limit results by specific dates you can use startDate and endDate fields instead.]
    * @param [enum] $sortBy [Lists results by ascending and descending order.]
    * @param [string] $startDate [Limit results to only after a specific date. Format: MM/DD/YYYY.]
    * @param [string] $endDate [Limit results to only before a specific date. Format: MM/DD/YYYY.]
    * @return [array] [Returns activity log about things like forms created/modified/deleted, account logins and other operations.]
    */
    public function getHistory($action = null, $date = null, $sortBy = null, $startDate = null, $endDate = null) {
        $params = $this->createHistoryQuery($action, $date, $sortBy, $startDate, $endDate);
        return $this->_executeGetRequest('user/history', $params);
    }

    /**
     * [getForm Get basic information about a form.]
     * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
     * @return [array] [Returns form ID, status, update and creation dates, submission count etc.]
     */
    public function getForm($formID) {
        return $this->_executeGetRequest('form/'. $formID);
    }

    /**
    * [getFormQuestions Get a list of all questions on a form.]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns question properties of a form.]
    */
    public function getFormQuestions($formID) {
        return $this->_executeGetRequest("form/{$formID}/questions");
    }

    /**
    *[getFormQuestion Get details about a question]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [integer] $qid [Identifier for each question on a form. You can get a list of question IDs from /form/{id}/questions.]
    * @return [array] [Returns question properties like required and validation.]
    */
    public function getFormQuestion($formID, $qid) {
        return $this->_executeGetRequest("form/{$formID}/question/{$qid}");
    }

    /**
     * [getFormSubmissions List of a form submissions]
     * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
     * @param [int] $offset [Start of each result set for form list. (optional)]
     * @param [int] $limit [Number of results in each result set for form list. (optional)]
     * @param [array] $filter [Filters the query results to fetch a specific form range.(optional)]
     * @param [string] $orderBy [Order results by a form field name. (optional)]
     * @return [array] [Returns submissions of a specific form.]
     */
    public function getFormSubmissions($formID, $offset = 0, $limit = 0, $filter = null, $orderby = null) {
        $params = $this->createConditions($offset, $limit, $filter, $orderby);
        return $this->_executeGetRequest("form/{$formID}/submissions", $params);
    }

    /**
     * [createFormSubmissions Submit data to this form using the API]
     * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
     * @param [array] $submission [Submission data with question IDs.]
     * @return [array] [Returns posted submission ID and URL.]
     */
    public function createFormSubmission($formID, $submission) {
        $sub = array();
        foreach ($submission as $key => $value) {
            if (strpos($key, '_')) {
                $qid = substr($key, 0, strpos($key, '_'));
                $type = substr($key, strpos($key, '_') + 1);
                $sub["submission[{$qid}][{$type}]"] = $value;
            } else {
                $sub["submission[{$key}]"] = $value;
            }
        }
        return $this->_executePostRequest("form/{$formID}/submissions", $sub);
    }

    /**
    * [createFormSubmissions Submit data to this form using the API]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [json] $submissions [Submission data with question IDs.]
    * @return [array]
    */
    public function createFormSubmissions($formID, $submissions) {
        return $this->_executePutRequest("form/".$formID."/submissions", $submissions);
    }

    /**
    * [getFormFiles List of files uploaded on a form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns uploaded file information and URLs on a specific form.]
    */
    public function getFormFiles($formID) {
        return $this->_executeGetRequest("form/{$formID}/files");
    }

    /**
    * [getFormWebhooks Get list of webhooks for a form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns list of webhooks for a specific form.]
    */
    public function getFormWebhooks($formID) {
        return $this->_executeGetRequest("form/{$formID}/webhooks");
    }

    /**
    * [createFormWebhook Add a new webhook]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [string] $webhookURL [Webhook URL is where form data will be posted when form is submitted.]
    * @return [array] [Returns list of webhooks for a specific form.]
    */
    public function createFormWebhook($formID, $webhookURL) {
        return $this->_executePostRequest("form/{$formID}/webhooks", array('webhookURL' => $webhookURL) );
    }

    /**
    * [deleteFormWebhook] [Delete a specific webhook of a form.]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [integer] $webhookID [You can get webhook IDs when you call /form/{formID}/webhooks.]
    * @return [array] [Returns remaining webhook URLs of form.]
    */
    public function deleteFormWebhook($formID, $webhookID) {
        return $this->_executeDeleteRequest("form/{$formID}/webhooks/{$webhookID}", null);
    }

    /**
    * [getSubmission Get submission data]
    * @param  [integer] $sid [You can get submission IDs when you call /form/{id}/submissions.]
    * @return [array] [Returns information and answers of a specific submission.]
    */
    public function getSubmission($sid) {
        return $this->_executeGetRequest("submission/{$sid}");
    }

    /**
    * [getReport Get report details]
    * @param  [integer] $reportID [You can get a list of reports from /user/reports.]
    * @return [array] [Returns properties of a speceific report like fields and status.]
    */
    public function getReport($reportID) {
        return $this->_executeGetRequest("report/{$reportID}");
    }

    /**
    * [getFolder Get folder details]
    * @param  [integer] $folderID [You can get a list of folders from /user/folders.]
    * @return [array] [Returns a list of forms in a folder, and other details about the form such as folder color.]
    */
    public function getFolder($folderID) {
        return $this->_executeGetRequest("folder/{$folderID}");
    }

    /**
    * [getFormProperties Get a list of all properties on a form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns form properties like width, expiration date, style etc.]
    */
    public function getFormProperties($formID) {
        return $this->_executeGetRequest("form/{$formID}/properties");
    }

    /**
    * [getFormProperty Get a specific property of the form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param  [string] $propertyKey [You can get property keys when you call /form/{id}/properties.]
    * @return [array] [Returns given property key value.]
    */
    public function getFormProperty($formID, $propertyKey) {
        return $this->_executeGetRequest("form/{$formID}/properties/{$propertyKey}");
    }

    /**
    * [getFormReports Get all the reports of a form, such as excel, csv, grid, html, etc.]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns a list of reports in a form, and other details about the reports such as title.]
    */
    public function getFormReports($formID) {
        return $this->_executeGetRequest("form/{$formID}/reports");
    }

    /**
    * [createReport Create new report of a form]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [array] $report [Report details. List type, title etc.]
    * @return [array] [Returns report details and URL.]
    */
    public function createReport($formID, $report) {
        return $this->_executePostRequest("form/{$formID}/reports", $report);
    }

    /**
    * [deleteSubmission Delete a single submission]
    * @param  [integer] $sid [You can get submission IDs when you call /user/submissions.]
    * @return [array] [Returns status of request.]
    */
    public function deleteSubmission($sid) {
        return $this->_executeDeleteRequest("submission/{$sid}");
    }

    /**
    * [editSubmission Edit a single submission]
    * @param  [integer] $sid [You can get submission IDs when you call /form/{id}/submissions.]
    * @param [array] $submission [New submission data with question IDs.]
    * @return [array] [Returns status of request.]
    */
    public function editSubmission($sid, $submission) {
        $sub = array();
        foreach ($submission as $key => $value) {
            if (strpos($key, '_') && $key != 'created_at') {
                $qid = substr($key, 0, strpos($key, '_'));
                $type = substr($key, strpos($key, '_') + 1);
                $sub["submission[{$qid}][{$type}]"] = $value;
            } else {
                $sub["submission[{$key}]"] = $value;
            }
        }
        return $this->_executePostRequest("submission/".$sid, $sub);
    }

    /**
    * [cloneForm Clone a single form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns status of request.]
    */
    public function cloneForm($formID) {
        return $this->_executePostRequest("form/{$formID}/clone", null);
    }

    /**
    * [deleteFormQuestion Delete a single form question]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [integer] $qid [Identifier for each question on a form. You can get a list of question IDs from /form/{id}/questions.]
    * @return [array] [Returns status of request.]
    */
    public function deleteFormQuestion($formID, $qid) {
        return $this->_executeDeleteRequest("form/{$formID}/question/{$qid}", null);
    }

    /**
    * [createFormQuestion Add new question to specified form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [array] $question [New question properties like type and text.]
    * @return [array] [Returns properties of new question.]
    */
    public function createFormQuestion($formID, $question) {
        $params = array();
        foreach ($question as $key => $value) {
            $params["question[{$key}]"] = $value;
        }
        return $this->_executePostRequest("form/{$formID}/questions", $params);
    }

    /**
    * [createFormQuestions Add new questions to specified form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [json] $questions [New question properties like type and text.]
    * @return [array] [Returns properties of new questions.]
    */
    public function createFormQuestions($formID, $questions) {
        return $this->_executePutRequest("form/{$formID}/questions", $questions);
    }

    /**
    * [editFormQuestion Add or edit a single question properties]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [integer] $qid [Identifier for each question on a form. You can get a list of question IDs from /form/{id}/questions.]
    * @param [array] $questionProperties [New question properties like text and order.]
    * @return [array] [Returns edited property and type of question.]
    */
    public function editFormQuestion($formID, $qid, $questionProperties) {
        $question = array();
        foreach ($questionProperties as $key => $value) {
            $question["question[{$key}]"] = $value;
        }
        return $this->_executePostRequest("form/{$formID}/question/{$qid}", $question);
    }

    /**
    * [setFormProperties Add or edit properties of a specific form]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [array] $formProperties [New properties like label width.]
    * @return [array] [Returns edited properties.]
    */
    public function setFormProperties($formID, $formProperties) {
        $properties = array();
        foreach ($formProperties as $key => $value) {
            $properties["properties[{$key}]"] = $value;
        }
        return $this->_executePostRequest("form/{$formID}/properties", $properties);
    }

    /**
    *[setMultipleFormProperties Add or edit properties of a specific form]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [json] $formProperties [New properties like label width.]
    * @return [array] [Returns edited properties.]
    */
    public function setMultipleFormProperties($formID, $formProperties) {
        return $this->_executePutRequest("form/{$formID}/properties", $formProperties);
    }

    /**
    * [createForm Create a new form]
    * @param [array] $form [Questions, properties and emails of new form.]
    * @return [array] [Returns new form.]
    */
    public function createForm($form) {
        $params = array();
        foreach ($form as $key => $value) {
            foreach ($value as $k => $v) {
                if ($key == "properties") {
                    $params["{$key}[{$k}]"] = $v;
                } else {
                    foreach ($v as $a => $b) {
                        $params["{$key}[{$k}][{$a}]"] = $b;
                    }
                }
            }
        }
        return $this->_executePostRequest('user/forms', $params);
    }

    /**
    * [createForm Create new forms]
    * @param [json] $form [Questions, properties and emails of forms.]
    * @return [array] [Returns new forms.]
    */
    public function createForms($forms) {
        return $this->_executePutRequest('user/forms', $forms);
    }

    /**
    * [deleteForm Delete a specific form]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return  [array] [Returns roperties of deleted form.]
    */
    public function deleteForm($formID) {
        return $this->_executeDeleteRequest("form/{$formID}", null);
    }

    /**
    * [registerUser Register with username, password and email]
    * @param [array] $userDetails [username, password and email to register a new user]
    * @return [array] [Returns new user's details]
    */
    public function registerUser($userDetails) {
        return $this->_executePostRequest('user/register', $userDetails);
    }

    /**
    * [loginUser Login user with given credentials]
    * @param [array] $credentials [Username, password, application name and access type of user]
    * @return [array] [Returns logged in user's settings and app key.]
    */
    public function loginUser($credentials) {
        return $this->_executePostRequest('user/login', $credentials);
    }

    /**
    * [logoutUser Logout User]
    * @return [array] [Status of request]
    */
    public function logoutUser() {
        return $this->_executeGetRequest('user/logout');
    }

    /**
    * [getPlan Get details of a plan]
    * @param [string] $planName [Name of the requested plan. FREE, PREMIUM etc.]
    * @return [array] [Returns details of a plan]
    */
    public function getPlan($planName) {
        return $this->_executeGetRequest("system/plan/{$planName}");
    }

    /**
    * [deleteReport Delete a specific report]
    * @param [integer] $formID [$reportID [You can get a list of reports from /user/reports.]
    * @return  [array] [Returns status of request.]
    */
    public function deleteReport($reportID) {
        return $this->_executeDeleteRequest("report/{$reportID}", null);
    }
}

class JotFormException extends Exception {}
