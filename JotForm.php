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

    /**
     * [_executeGetRequest description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    private function _executeHttpRequest($path, $params=array(), $method){

        $url = implode("/", array($this->baseUrl, $this->apiVersion,$path));

        $this->_debugDump($params);

        if ($method=="GET"){
            $params_array = array();
            foreach ($params as $key => $value) {
                $params_array[] = $key ."=". $value;
            }
            $params_string = "?" . implode("&",$params_array);
            unset($params_array);
            $url = $url.$params_string;
        }

        $this->_debugLog("params string".$params_string);

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

    /**
     * Returns User object
     * @return [type] [description]
     */
    public function getUser(){
        $res = $this->_executeGetRequest("user");
        return $res;
    }

    /**
    * [getUserUsage description]
    * @return [type] [description]
    */
    public function getUsage(){
        return $this->_executeGetRequest("user/usage");
    }

    /**
     * [getForms description]
     * @return [type] [description]
     */
    public function getForms(){
        return $this->_executeGetRequest("user/forms");
    }

    /**
     * [getSubmissions description]
     * @return [type] [description]
     */
    public function getSubmissions(){
        return $this->_executeGetRequest("user/submissions");
    }

    /**
    * [getUserSubusers description]
    * @return [type] [description]
    */
    public function getSubusers(){
        return $this->_executeGetRequest("user/subusers");
    }

    /**
    * [getUserFolders description]
    * @return [type] [description]
    */
    public function getFolders(){
        return $this->_executeGetRequest("user/folders");
    }

    /**
    * [getUserReports description]
    * @return [type] [description]
    */
    public function getReports(){
        return $this->_executeGetRequest("user/reports");
    }

    /**
    * [getUserSettings description]
    * @return [type]  [description]
    */
    public function getSettings(){
        return $this->_executeGetRequest("user/settings");
    }

    /**
    * [getUserHistory description]
    * @return [type] [description]
    */
    public function getHistory(){
        return $this->_executeGetRequest("user/history");
    }

    /**
     * [getForm description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getForm($formID){
        return $this->_executeGetRequest("form/". $formID);
    }

    /**
    * [getQuestionsByFormId description]
    * @param  [type] $formID [description]
    * @return [type]         [description]
    */
    public function getFormQuestions($formID){
        return $this->_executeGetRequest("form/".$formID."/questions");
    }

    /**
    *[getQuestionByQuestionId description]
    * @param [type] $formID [description]
    * @param [type] $questionID [description]
    * @return [type]     [description]
    */
    public function getFormQuestion($formID, $qid){
        return $this->_executeGetRequest("form/".$formID."/question/".$qid);
    }

    /**
     * [getSubmissionsByFormId description]
     * @param  [type] $formID [description]
     * @return [type]         [description]
     */
    public function getFormSubmissions($formID){
        return $this->_executeGetRequest("form/". $formID ."/submissions");
    }

    /**
     * [postSubmissionsByFormId description]
     * @param  [type] $formID [description]
     * @return [type]         [description]
     */
    public function createFormSubmissions($formID, $submission){
        return $this->_executePostRequest("form/". $formID ."/submissions", array('submission' => $submission));
    }

    /**
    * [getFilesByFormId description]
    * @param  [type] $formID [decription]
    * @return [type]         [decription]
    */
    public function getFormFiles($formID){
        return $this->_executeGetRequest("form/".$formID."/files");
    }

    /**
    * [getWebhooksByFormID description]
    * @param  [type] $formID [description]
    * @return [type]         [description]
    */
    public function getFormWebhooks($formID){
        return $this->_executeGetRequest("form/".$formID."/webhooks");
    }

    /**
    * [addWebhookToForm description]
    * @param  [type] $formID [description]
    * @return [type]         [description]
    */
    public function createFormWebhook($formID, $webhookURL){
        return $this->_executePostRequest("form/".$formID."/webhooks",array("webhookURL" => $webhookURL) );
    }

    /**
    * [getUserSubmissionById description]
    * @param  [type] $subId [description]
    * @return [type]        [description]
    */
    public function getSubmission($sid){
        return $this->_executeGetRequest("submission/".$sid);
    }

    /**
    * [getUserReportById description]
    * @param  [type] $subId [description]
    * @return [type] [description]
    */
    public function getReport($reportID){
        return $this->_executeGetRequest("report/".$reportID);
    }

    /**
    * [getFolderById description]
    * @param  [type] $subId [description]
    * @return [type] [description]
    */
    public function getFolder($folderID) {
        return $this->_executeGetRequest("folder/".$folderID);
    }
}

class JotFormException extends Exception{}
