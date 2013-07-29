<?

/**
 * JotForm API - PHP Client
 *
 * @copyright   2013 Interlogy, LLC.
 * @link        http://www.jotform.com
 * @version     0.1
 * @package     JotFormAPI
 */
class JotForm {


    public function __construct($apiKey, $debugMode=false){

        if (!$apiKey){
            throw new JotFormException("apiKey is required");
        }

        $this->apiKey = $apiKey;

        $this->debugMode = $debugMode;
        $this->baseUrl = "http://api.jotform.com";
        $this->apiVersion = "v1";
    }

    private function _debugLog($str){
        if ($this->debugMode){
            print_r("\n");
            print_r($str);
        }
    }

    private function _debugDump($obj){
        if ($this->debugMode){
            print_r("\n");
            var_dump($obj);
        }
    }

    private function _executeHttpRequest($path, $params=array(), $method){

        $url = implode("/", array($this->baseUrl, $this->apiVersion,$path));

        $this->_debugDump($params);

        if ($method=="GET" && $params != null){
            $params_array = array();
            foreach ($params as $key => $value) {
                $params_array[] = $key ."=". $value;
            }
            $params_string = "?" . implode("&",$params_array);
            unset($params_array);
            $url = $url.$params_string;
            $this->_debugLog("params string".$params_string);
            
        }

        $this->_debugLog("fetching url : ".$url);

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_USERAGENT,"JOTFORM_PHP_WRAPPER");
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('APIKEY: '.$this->apiKey));

        if ($method=="POST"){
            $this->_debugLog("posting");
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $params);
        }

        if ($method=="DELETE"){
            $this->_debugLog("delete");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
        }

        $result = curl_exec($ch);

        if ($result == false){
            throw new Exception('Unable to');
        }

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->_debugLog("http code is : ".$http_status);

        // TODO : check if output type is xml and return accordingly
        $result_obj = json_decode($result,true);

        /*
         * Handle HTTP Errors
         */
        
        if ($http_status != 200) {

            if ($http_status == 401) {
                throw new JotFormException("Unauthorized API call");
            } else if ($http_status == 404) {
                throw new JotFormException($result_obj["message"]);
            } else if ($http_status == 503) {
                throw new JotFormException("Service is unavailable, rate limits etc exceeded!");
            }
            throw new JotFormException($result_obj["info"]);
        }

        curl_close($ch);

        return $result_obj["content"];
    }

    private function _executeGetRequest($url, $params=array()){
        return $this->_executeHttpRequest($url, $params, "GET");
    }

    private function _executePostRequest($url, $params){
        return $this->_executeHttpRequest($url, $params, "POST");
    }

    private function _executeDeleteRequest($url, $params=array()){
        return $this->_executeHttpRequest($url, $params, "DELETE");
    }

    private function createConditions($offset, $limit, $filter, $orderBy) {

        foreach(array("offset", "limit", "filter", "orderBy") as $arg) {
             if(${$arg}) {
                 $params[$arg] = ${$arg};
                 if($arg == "filter") {
                     $params[$arg] = json_encode($params[$arg]);
                 }
             }
        }

        return $params;
    }

    /**
     * [getUser Get user account details for a JotForm user]
     * @return [array] [Returns user account type, avatar URL, name, email, website URL and account limits.]
     */
    public function getUser(){
        $res = $this->_executeGetRequest("user");
        return $res;
    }

    /**
    * [getUserUsage Get number of form submissions received this month]
    * @return [array] [Returns number of submissions, number of SSL form submissions, payment form submissions and upload space used by user.]
    */
    public function getUsage(){
        return $this->_executeGetRequest("user/usage");
    }

    /**
     * [getForms Get a list of forms for this account]
     * @param [integer] $offset [Start of each result set for form list. (optional)]
     * @param [integer] $limit [Number of results in each result set for form list. (optional)]
     * @param [array] $filter [Filters the query results to fetch a specific form range.(optional)]
     * @param [string] $orderBy [Order results by a form field name. (optional)]
     * @return [array] [Returns basic details such as title of the form, when it was created, number of new and total submissions.]
     */
    public function getForms($offset = 0, $limit = 0, $filter = null, $orderBy = null){
        $params = $this->createConditions($offset, $limit, $filter, $orderBy);

        return $this->_executeGetRequest("user/forms", $params);
    }

    /**
     * [getSubmissions Get a list of submissions for this account]
     * @param [int] $offset [Start of each result set for form list. (optional)]
     * @param [int] $limit [Number of results in each result set for form list. (optional)]
     * @param [array] $filter [Filters the query results to fetch a specific form range.(optional)]
     * @param [string] $orderBy [Order results by a form field name. (optional)]
     * @return [array] [Returns basic details such as title of the form, when it was created, number of new and total submissions.]
     */
    public function getSubmissions($offset = 0, $limit = 0, $filter = null, $orderBy = null){
        $params = $this->createConditions($offset, $limit, $filter, $orderBy);

        return $this->_executeGetRequest("user/submissions", $params);
    }

    /**
    * [getUserSubusers Get a list of sub users for this account]
    * @return [array] [Returns list of forms and form folders with access privileges.]
    */
    public function getSubusers(){
        return $this->_executeGetRequest("user/subusers");
    }

    /**
    * [getUserFolders Get a list of form folders for this account]
    * @return [array] [Returns name of the folder and owner of the folder for shared folders.]
    */
    public function getFolders(){
        return $this->_executeGetRequest("user/folders");
    }

    /**
    * [getReports List of URLS for reports in this account]
    * @return [array] [Returns reports for all of the forms. ie. Excel, CSV, printable charts, embeddable HTML tables.]
    */
    public function getReports(){
        return $this->_executeGetRequest("user/reports");
    }

    /**
    * [getSettings Get user's settings for this account]
    * @return [array]  [Returns user's time zone and language.]
    */
    public function getSettings(){
        return $this->_executeGetRequest("user/settings");
    }

    /**
    * [getHistory Get user activity log]
    * @return [array] [Returns activity log about things like forms created/modified/deleted, account logins and other operations.]
    */
    public function getHistory(){
        return $this->_executeGetRequest("user/history");
    }

    /**
     * [getForm Get basic information about a form.]
     * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
     * @return [array] [Returns form ID, status, update and creation dates, submission count etc.]
     */
    public function getForm($formID){
        return $this->_executeGetRequest("form/". $formID);
    }

    /**
    * [getFormQuestions Get a list of all questions on a form.]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns question properties of a form.]
    */
    public function getFormQuestions($formID){
        return $this->_executeGetRequest("form/".$formID."/questions");
    }

    /**
    *[getFormQuestion Get details about a question]
    * @param [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [integer] $qid [Identifier for each question on a form. You can get a list of question IDs from /form/{id}/questions.]
    * @return [array] [Returns question properties like required and validation.]
    */
    public function getFormQuestion($formID, $qid){
        return $this->_executeGetRequest("form/".$formID."/question/".$qid);
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
    public function getFormSubmissions($formID, $offset = 0, $limit = 0, $filter = null, $orderBy = null){
        $params = $this->createConditions($offset, $limit, $filter, $orderBy);

        return $this->_executeGetRequest("form/". $formID ."/submissions", $params);
    }

    /**
     * [createFormSubmissions Submit data to this form using the API]
     * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
     * @param [array] $submission [Submission data with question IDs.]
     * @return [array] [Returns posted submission ID and URL.]
     */
    public function createFormSubmissions($formID, $submission){
        $sub = array();

        foreach ($submission as $key => $value) {
            if (strpos($key, "_")) {
                $qid = substr($key, 0, strpos($key, "_"));
                $type = substr($key, strpos($key, "_") + 1);
                $sub["submission[$qid][$type]"] = $value;
            } else {
                $sub["submission[".$key."]"] = $value;
            }
        }

        return $this->_executePostRequest("form/". $formID ."/submissions", $sub);
    }

    /**
    * [getFormFiles List of files uploaded on a form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns uploaded file information and URLs on a specific form.]
    */
    public function getFormFiles($formID){
        return $this->_executeGetRequest("form/".$formID."/files");
    }

    /**
    * [getFormWebhooks Get list of webhooks for a form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns list of webhooks for a specific form.]
    */
    public function getFormWebhooks($formID){
        return $this->_executeGetRequest("form/".$formID."/webhooks");
    }

    /**
    * [createFormWebhook Add a new webhook]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [string] $webhookURL [Webhook URL is where form data will be posted when form is submitted.]
    * @return [array] [Returns list of webhooks for a specific form.]
    */
    public function createFormWebhook($formID, $webhookURL){
        return $this->_executePostRequest("form/".$formID."/webhooks",array("webhookURL" => $webhookURL) );
    }

    /**
    * [getSubmission Get submission data]
    * @param  [integer] $sid [You can get submission IDs when you call /form/{id}/submissions.]
    * @return [array] [Returns information and answers of a specific submission.]
    */
    public function getSubmission($sid){
        return $this->_executeGetRequest("submission/".$sid);
    }

    /**
    * [getReport Get report details]
    * @param  [integer] $reportID [You can get a list of reports from /user/reports.]
    * @return [array] [Returns properties of a speceific report like fields and status.]
    */
    public function getReport($reportID){
        return $this->_executeGetRequest("report/".$reportID);
    }

    /**
    * [getFolder Get folder details]
    * @param  [integer] $folderID [You can get a list of folders from /user/folders.]
    * @return [array] [Returns a list of forms in a folder, and other details about the form such as folder color.]
    */
    public function getFolder($folderID) {
        return $this->_executeGetRequest("folder/".$folderID);
    }

    /**
    * [getFormProperties Get a list of all properties on a form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns form properties like width, expiration date, style etc.]
    */
    public function getFormProperties($formID) {
        return $this->_executeGetRequest("form/".$formID."/properties");
    }

    /**
    * [getFormProperty Get a specific property of the form]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param  [string] $propertyKey [You can get property keys when you call /form/{id}/properties.]
    * @return [array] [Returns given property key value.]
    */
    public function getFormProperty($formID, $propertyKey) {
        return $this->_executeGetRequest("form/".$formID."/properties/".$propertyKey);
    }

    /**
    * [deleteSubmission Delete a single submission]
    * @param  [integer] $sid [You can get submission IDs when you call /user/submissions.]
    * @return [array] [Returns status of request.]
    */
    public function deleteSubmission($sid) {
        return $this->_executeDeleteRequest("submission/".$sid);
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
            if (strpos($key, "_")) {
                $qid = substr($key, 0, strpos($key, "_"));
                $type = substr($key, strpos($key, "_") + 1);
                $sub["submission[$qid][$type]"] = $value;
            } else {
                $sub["submission[".$key."]"] = $value;
            }
        }

        return $this->_executePostRequest("submission/".$sid, $sub);
    }

    /**
    * [cloneForm Clone a single form.]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @return [array] [Returns status of request.]
    */
    public function cloneForm($formID) {
        return $this->_executePostRequest("form/".$formID."/clone", null);
    }

    /**
    * [deleteFormQuestion Delete a single form question.]
    * @param  [integer] $formID [Form ID is the numbers you see on a form URL. You can get form IDs when you call /user/forms.]
    * @param [integer] $qid [Identifier for each question on a form. You can get a list of question IDs from /form/{id}/questions.]
    * @return [array] [Returns status of request.]
    */
    public function deleteFormQuestion($formID, $qid) {
        return $this->_executeDeleteRequest("form/".$formID."/question/".$qid, null);
    }
}

class JotFormException extends Exception{}
