<?php

namespace App\Utils\Libs\Tim;

class TimRestAPI extends TimRestInterface
{
    const REQUEST_URL = 'https://console.tim.qq.com/v4/%s/%s?usersig=%s&identifier=%s&sdkappid=%s&contenttype=json';

    protected $sdkappid = 0;
    protected $usersig = '';
    public $identifier = '';
    public $sigApi;

    /**
     * @inheritDoc
     */
     function init($sdkappid, $identifier, $key)
    {
        $this->sdkappid = $sdkappid;
        $this->identifier = $identifier;
        $this->sigApi = new TLSSigAPIv2($sdkappid, $key);
    }

    public function setUserSig($identifier, $expiry_after)
    {
        $this->usersig = $this->generate_user_sig($identifier, $expiry_after);
    }

    /**
     * @inheritDoc
     */
    public function generate_user_sig($identifier, $expiry_after)
    {
        try {
            return $this->sigApi->genSig($identifier, $expiry_after);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function set_user_sig($usr_sig)
    {
        $this->usersig = $usr_sig;
        return true;
    }

    /**
     * @inheritDoc
     */
    function openim_send_msg($account_id, $receiver, $text_content)
    {
        $msg_content = [];
        $msg_content_elem = [
            'MsgType' => 'TIMTextElem',
            'MsgContent' => [
                'Text' => $text_content,
            ]
        ];
        $msg_content[] = $msg_content_elem;
        return $this->openim_send_msg2($account_id, $receiver, $msg_content);
    }

    /**
     * @inheritDoc
     */
    function openim_send_msg_pic($account_id, $receiver, $pic_path)
    {
        $busi_type = 2; //表示C2C消息
        $ret = $this->openpic_pic_upload($account_id, $receiver, $pic_path, $busi_type);
        $tmp = $ret["URL_INFO"];

        $uuid = $ret["File_UUID"];
        $pic_url = $tmp[0]["DownUrl"];

        $img_info = array();
        $img_tmp = $ret["URL_INFO"][0];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem1 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_tmp = $ret["URL_INFO"][1];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem2 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_tmp = $ret["URL_INFO"][2];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem3 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_info[] = $img_info_elem1;
        $img_info[] = $img_info_elem2;
        $img_info[] = $img_info_elem3;
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMImageElem',       //文本类型
            'MsgContent' => array(
                'UUID' => $uuid,
                'ImageInfoArray' => $img_info,
            )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        $msg_content[] = $msg_content_elem;

        return $this->openim_send_msg2($account_id, $receiver, $msg_content);
    }

    /**
     * @inheritDoc
     */
    function openim_send_msg2($account_id, $receiver, $msg_content)
    {
        $msg = [
            'To_Account' => $receiver,
            'MsgSeq' => rand(1, 65535),
            'MsgRandom' => rand(1, 65535),
            'MsgTimeStamp' => time(),
            'MsgBody' => $msg_content,
            'From_Account' => $account_id
        ];
        return $this->comm_rest('openim', 'sendmsg', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function openim_batch_sendmsg($account_list, $text_content)
    {
        $msg_content = [];
        $msg_content_elem = [
            'MsgType' => 'TIMTextElem',
            'MsgContent' => [
                'Text' => $text_content,
            ]
        ];
        $msg_content[] = $msg_content_elem;
        return $this->openim_batch_sendmsg2($account_list, $msg_content);
    }

    /**
     * @inheritDoc
     */
    function openim_batch_sendmsg_pic($account_list, $pic_path)
    {
        $busi_type = 2; //表示C2C消息
        $ret = $this->openpic_pic_upload($this->identifier, $account_list[0], $pic_path, $busi_type);
        $tmp = $ret["URL_INFO"];

        $uuid = $ret["File_UUID"];
        $pic_url = $tmp[0]["DownUrl"];

        $img_info = array();
        $img_tmp = $ret["URL_INFO"][0];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem1 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_tmp = $ret["URL_INFO"][1];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem2 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_tmp = $ret["URL_INFO"][2];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem3 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_info[] = $img_info_elem1;
        $img_info[] = $img_info_elem2;
        $img_info[] = $img_info_elem3;
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMImageElem',       //文本类型
            'MsgContent' => array(
                'UUID' => $uuid,
                'ImageInfoArray' => $img_info,
            )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        $msg_content[] = $msg_content_elem;

        return $this->openim_batch_sendmsg2($account_list, $msg_content);
    }

    /**
     * @inheritDoc
     */
    function openim_batch_sendmsg2($account_list, $msg_content)
    {
        $msg = [
            'To_Account' => $account_list,
            'MsgRandom' => rand(1, 65535),
            'MsgBody' => $msg_content,
        ];
        return $this->comm_rest('openim', 'batchsendmsg', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function account_import($identifier, $nick, $face_url)
    {
        $msg = [
            'Identifier' => (string)$identifier,
            'Nick' => $nick,
            'FaceUrl' => $face_url,
        ];
        return $this->comm_rest('im_open_login_svc', 'account_import', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    public function register_account($identifier, $identifierType, $password)
    {
        $msg = [
            'Identifier' => $identifier,
            'IdentifierType' => $identifierType,
            'Password' => $password,
        ];
        return $this->comm_rest('registration_service', 'register_account_v1', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function profile_portrait_get($account_id)
    {
        $account_list = [];
        $account_list[] = $account_id;
        $tag_list = [
            "Tag_Profile_IM_Nick",
            "Tag_Profile_IM_AllowType"
        ];
        return $this->profile_portrait_get2($account_list, $tag_list);
    }

    /**
     * @inheritDoc
     */
    function profile_portrait_get2($account_list, $tag_list)
    {
        $msg = [
            'From_Account' => $this->identifier,
            'To_Account' => $account_list,
            'TagList' => $tag_list,
            'LastStandardSequence' => 0
        ];
        return $this->comm_rest('profile', 'portrait_get', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function profile_portrait_set($account_id, $new_name)
    {
        $profile_list = [];
        $profile_nick = [
            "Tag" => "Tag_Profile_IM_Nick",
            "Value" => $new_name
        ];
        $profile_list[] = $profile_nick;
        return $this->profile_portrait_set2($account_id, $profile_list);
    }

    /**
     * @inheritDoc
     */
    function profile_portrait_set2($account_id, $profile_list)
    {
        $msg = [
            'From_Account' => (string)$account_id,
            'ProfileItem' => $profile_list
        ];
        return $this->comm_rest('profile', 'portrait_set', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function sns_friend_import($account_id, $receiver)
    {
        $msg = [
            'From_Account' => $account_id,
            'AddFriendItem' => []
        ];
        $receiver_arr = [
            'To_Account' => $receiver,
            'Remark' => "",
            'AddSource' => "AddSource_Type_Unknow",
            'AddWording' => ""
        ];
        $msg['AddFriendItem'][] = $receiver_arr;
        return $this->comm_rest('sns', 'friend_import', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function sns_friend_delete($account_id, $frd_id)
    {
        $frd_list = [];
        $frd_list[] = $frd_id;
        $msg = [
            'From_Account' => $account_id,
            'To_Account' => $frd_list,
            'DeleteType' => "Delete_Type_Both"
        ];
        return $this->comm_rest('sns', 'friend_delete', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function sns_friend_delete_all($account_id)
    {
        $msg = [
            'From_Account' => $account_id,
        ];
        return $this->comm_rest('sns', 'friend_delete_all', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function sns_friend_check($account_id, $to_account)
    {
        $to_account_list = [];
        $to_account_list[] = $to_account;
        return $this->sns_friend_check2($account_id, $to_account_list, "CheckResult_Type_Both");
    }

    /**
     * @inheritDoc
     */
    function sns_friend_check2($account_id, $to_account_list, $check_type)
    {
        $msg = [
            'From_Account' => $account_id,
            'To_Account' => $to_account_list,
            'CheckType' => $check_type
        ];
        return $this->comm_rest('sns', 'friend_check', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function sns_friend_get_all($account_id)
    {
        $tag_list = [
            "Tag_Profile_IM_Nick",
            "Tag_SNS_IM_Remark"
        ];
        return $this->sns_friend_get_all2($account_id, $tag_list);
    }

    /**
     * @inheritDoc
     */
    function sns_friend_get_all2($account_id, $tag_list)
    {
        $msg = [
            'From_Account' => $account_id,
            'TimeStamp' => 0,
            'TagList' => $tag_list,
            'LastStandardSequence' => 1,
        ];
        return $this->comm_rest('sns', 'friend_get_all', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function sns_friend_get_list($account_id, $frd_id)
    {
        $frd_list = [];
        $frd_list[] = $frd_id;
        $tag_list = [
            "Tag_Profile_IM_Nick",
            "Tag_SNS_IM_Remark"
        ];
        return $this->sns_friend_get_list2($account_id, $frd_list, $tag_list);
    }

    /**
     * @inheritDoc
     */
    function sns_friend_get_list2($account_id, $frd_list, $tag_list)
    {
        $msg = [
            'From_Account' => $account_id,
            'To_Account' => $frd_list,
            'TagList' => $tag_list,
        ];
        return $this->comm_rest('sns', 'friend_get_list', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_get_appid_group_list()
    {
        return $this->group_get_appid_group_list2(50, null, null);
    }

    /**
     * @inheritDoc
     */
    function group_get_appid_group_list2($limit, $offset, $group_type)
    {
        $msg = array(
            'Limit' => $limit,
            'Offset' => $offset,
            'GroupType' => $group_type
        );
        return $this->comm_rest('group_open_http_svc', 'get_appid_group_list', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_create_group($group_type, $group_name, $owner_id)
    {
        $info_set = [
            'group_id' => null,
            'introduction' => null,
            'notification' => null,
            'face_url' => null,
            'max_member_num' => 500,
        ];
        $mem_list = [];
        return $this->group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list);
    }

    /**
     * @inheritDoc
     */
    function group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list)
    {
        $msg = [
            'Type' => $group_type,
            'Name' => $group_name,
            'Owner_Account' => $owner_id,
            'GroupId' => $info_set['group_id'],
            'Introduction' => $info_set['introduction'],
            'Notification' => $info_set['notification'],
            'FaceUrl' => $info_set['face_url'],
            'MaxMemberCount' => $info_set['max_member_num'],
            'MemberList' => $mem_list
        ];
        return $this->comm_rest('group_open_http_svc', 'create_group', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_create_group3($group_type, $group_name, $owner_id, $group_id)
    {
        $info_set = [
            'group_id' => $group_id,
            'introduction' => null,
            'notification' => null,
            'face_url' => null,
            'max_member_num' => 500,
        ];
        $mem_list = [];
        return $this->group_create_group2($group_type, $group_name, $owner_id, $info_set, $mem_list);
    }

    /**
     * @inheritDoc
     */
    function group_change_group_owner($group_id, $new_owner)
    {
        $msg = [
            'GroupId' => $group_id,
            'NewOwner_Account' => $new_owner
        ];
        return $this->comm_rest('group_open_http_svc', 'change_group_owner', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_get_group_info($group_id)
    {
        $group_list = [];
        $group_list[] = $group_id;

        $base_info_filter = [
            "Type",               //群类型(包括Public(公开群), Private(私密群), ChatRoom(聊天室))
            "Name",               //群名称
            "Introduction",       //群简介
            "Notification",       //群公告
            "FaceUrl",            //群头像url地址
            "CreateTime",         //群组创建时间
            "Owner_Account",      //群主id
            "LastInfoTime",       //最后一次系统通知时间
            "LastMsgTime",        //最后一次消息发送时间
            "MemberNum",          //群组当前成员数目
            "MaxMemberNum",       //群组内最大成员数目
            "ApplyJoinOption"     //加群处理方式(比如FreeAccess 自由加入)
        ];
        $member_info_filter = [
            "Account",         // 成员ID
            "Role",            // 成员身份
            "JoinTime",        // 成员加入时间
            "LastSendMsgTime", // 该成员最后一次发送消息时间
            "ShutUpUntil"      // 该成员被禁言直到某时间
        ];
        $app_define_filter = [
            "GroupTestData1",  //自定义数据
        ];

        return $this->group_get_group_info2($group_list, $base_info_filter, $member_info_filter, $app_define_filter);
    }

    /**
     * @inheritDoc
     */
    function group_get_group_info2($group_list, $base_info_filter, $member_info_filter, $app_define_filter)
    {
        $filter = new Filter();
        // $filter->GroupBaseInfoFilter = $base_info_filter;
        // $filter->MemberInfoFilter = $member_info_filter;
        // $filter->AppDefinedDataFilter_Group = $app_define_filter;
        $msg = [
            'GroupIdList' => $group_list,
            'ResponseFilter' => $filter
        ];
        return $this->comm_rest('group_open_http_svc', 'get_group_info', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_get_group_member_info($group_id, $limit, $offset)
    {
        $msg = [
            "GroupId" => $group_id,
            "Limit" => $limit,
            "Offset" => $offset
        ];
        #将消息序列化为json串
        $req_data = json_encode($msg);
        return $this->comm_rest('group_open_http_svc', 'get_group_member_info', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_modify_group_base_info($group_id, $group_name)
    {
        $info_set = [
            'introduction' => null,
            'notification' => null,
            'face_url' => null,
            'max_member_num' => null,
            //	'apply_join' => "NeedPermission"
        ];
        $app_define_list = [];
        return $this->group_modify_group_base_info2($group_id, $group_name, $info_set, $app_define_list);
    }

    /**
     * @inheritDoc
     */
    function group_modify_group_base_info2($group_id, $group_name, $info_set, $app_define_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "Name" => $group_name,
            "Introduction" => $info_set['introduction'],
            "Notification" => $info_set['notification'],
            "FaceUrl" => $info_set['face_url'],
            "MaxMemberNum" => $info_set['max_member_num'],
            //	"ApplyJoinOption" => $info_set['apply_join'],
            "AppDefinedData" => $app_define_list
        ];
        return $this->comm_rest('group_open_http_svc', 'modify_group_base_info', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_add_group_member($group_id, $member_id, $silence)
    {
        $mem_list = [];
        $mem_elem = [
            "Member_Account" => $member_id
        ];
        $mem_list[] = $mem_elem;
        $msg = [
            "GroupId" => $group_id,
            "MemberList" => $mem_list,
            "Silence" => $silence
        ];
        return $this->comm_rest('group_open_http_svc', 'add_group_member', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_delete_group_member($group_id, $member_id, $silence)
    {
        $mem_list = [];
        $mem_list[] = $member_id;
        $msg = [
            "GroupId" => $group_id,
            "MemberToDel_Account" => $mem_list,
            "Silence" => $silence
        ];
        return $this->comm_rest('group_open_http_svc', 'delete_group_member', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_modify_group_member_info($group_id, $account_id, $role)
    {
        return $this->group_modify_group_member_info2($group_id, $account_id, $role, "AcceptAndNotify", 0);
    }

    /**
     * @inheritDoc
     */
    function group_modify_group_member_info2($group_id, $account_id, $role, $msg_flag, $shutup_time)
    {
        $msg = [
            "GroupId" => $group_id,
            "Member_Account" => $account_id,
            "Role" => $role
        ];
        return $this->comm_rest('group_open_http_svc', 'modify_group_member_info', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_destroy_group($group_id)
    {
        $msg = [
            "GroupId" => $group_id,
        ];
        return $this->comm_rest('group_open_http_svc', 'destroy_group', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_get_joined_group_list($account_id)
    {
        $base_info_filter = [
            "Type",               //群类型(包括Public(公开群), Private(私密群), ChatRoom(聊天室))
            "Name",               //群名称
            "Introduction",       //群简介
            "Notification",       //群公告
            "FaceUrl",            //群头像url地址
            "CreateTime",         //群组创建时间
            "Owner_Account",      //群主id
            "LastInfoTime",       //最后一次系统通知时间
            "LastMsgTime",        //最后一次消息发送时间
            "MemberNum",          //群组当前成员数目
            "MaxMemberNum",       //群组内最大成员数目
            "ApplyJoinOption"     //申请加群处理方式(比如FreeAccess 自由加入, NeedPermission 需要同意)
        ];

        $self_info_filter = [
            "Role",            //群内身份(Amin/Member)
            "JoinTime",        //入群时间
            "MsgFlag",         //消息屏蔽类型
            "UnreadMsgNum"     //未读消息数量
        ];

        return $this->group_get_joined_group_list2($account_id, null, $base_info_filter, $self_info_filter);
    }

    /**
     * @inheritDoc
     */
    function group_get_joined_group_list2($account_id, $group_type, $base_info_filter, $self_info_filter)
    {
        $filter = new Filter();
        // $filter->GroupBaseInfoFilter = $base_info_filter;
        // $filter->SelfInfoFilter = $self_info_filter;
        $msg = array(
            "Member_Account" => $account_id,
            "ResponseFilter" => $filter
        );
        return $this->comm_rest('group_open_http_svc', 'get_joined_group_list', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_get_role_in_group($group_id, $member_id)
    {
        $mem_list = [];
        $mem_list[] = $member_id;
        $msg = [
            "GroupId" => $group_id,
            "User_Account" => $mem_list,
        ];
        return $this->comm_rest('group_open_http_svc', 'get_role_in_group', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_forbid_send_msg($group_id, $member_id, $second)
    {
        $mem_list = [];
        $mem_list[] = $member_id;
        $msg = [
            "GroupId" => $group_id,
            "Members_Account" => $mem_list,
            "ShutUpTime" => $second
        ];
        return $this->comm_rest('group_open_http_svc', 'forbid_send_msg', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_send_group_msg($account_id, $group_id, $text_content)
    {
        $msg_content = [];
        $msg_content_elem = [
            'MsgType' => 'TIMTextElem',
            'MsgContent' => [
                'Text' => $text_content,
            ]
        ];
        $msg_content[] = $msg_content_elem;
        return $this->group_send_group_msg2($account_id, $group_id, $msg_content);
    }

    /**
     * @inheritDoc
     */
    function group_send_group_msg_pic($account_id, $group_id, $pic_path)
    {
        $busi_type = 1; //表示群消息
        $ret = $this->openpic_pic_upload($account_id, $group_id, $pic_path, $busi_type);
        $tmp = $ret["URL_INFO"];

        $uuid = $ret["File_UUID"];
        $pic_url = $tmp[0]["DownUrl"];

        $img_info = array();
        $img_tmp = $ret["URL_INFO"][0];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem1 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_tmp = $ret["URL_INFO"][1];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem2 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_tmp = $ret["URL_INFO"][2];
        if ($img_tmp["PIC_TYPE"] == 4) {
            $img_tmp["PIC_TYPE"] = 3;
        }
        $img_info_elem3 = array(
            "URL" => $img_tmp["DownUrl"],
            "Height" => $img_tmp["PIC_Height"],
            "Size" => $img_tmp["PIC_Size"],
            "Type" => $img_tmp["PIC_TYPE"],
            "Width" => $img_tmp["PIC_Width"]
        );

        $img_info[] = $img_info_elem1;
        $img_info[] = $img_info_elem2;
        $img_info[] = $img_info_elem3;
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMImageElem',       //文本类型
            'MsgContent' => array(
                'UUID' => $uuid,
                'ImageInfoArray' => $img_info,
            )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        $msg_content[] = $msg_content_elem;

        return $this->group_send_group_msg2($account_id, $group_id, $msg_content);
    }

    /**
     * @inheritDoc
     */
    function group_send_group_msg2($account_id, $group_id, $msg_content)
    {
        $msg = [
            "GroupId" => $group_id,
            "From_Account" => $account_id,
            "Random" => rand(1, 65535),
            "MsgBody" => $msg_content
        ];
        return $this->comm_rest('group_open_http_svc', 'send_group_msg', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_send_group_system_notification($group_id, $content, $receiver_id)
    {
        $receiver_list = [];
        if ($receiver_id != null) {
            $receiver_list[] = $receiver_id;
        }
        return $this->group_send_group_system_notification2($group_id, $content, $receiver_list);
    }

    /**
     * @inheritDoc
     */
    function group_send_group_system_notification2($group_id, $content, $receiver_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "ToMembers_Account" => $receiver_list,
            "Content" => $content
        ];
        return $this->comm_rest('group_open_http_svc', 'send_group_system_notification', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_import_group_member($group_id, $member_id, $role)
    {
        $member_list = [];
        $member_elem = [
            "Member_Account" => $member_id,
            "Role" => $role
        ];
        $member_list[] = $member_elem;
        return $this->group_import_group_member2($group_id, $member_list);
    }

    /**
     * @inheritDoc
     */
    function group_import_group_member2($group_id, $member_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "MemberList" => $member_list,
        ];
        return $this->comm_rest('group_open_http_svc', 'import_group_member', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_import_group_msg($group_id, $from_account, $text)
    {
        $msg_content = [
            "Text" => $text
        ];
        $msg_body_elem = [
            "MsgType" => "TIMTextElem",
            "MsgContent" => $msg_content,
        ];
        $msg_body_list = [];
        $msg_body_list[] = $msg_body_elem;

        //构造MsgList的一个元素
        $msg_list_elem = [
            "From_Account" => $from_account,
            "SendTime" => time(),
            "Random" => rand(1, 65535),
            "MsgBody" => $msg_body_list
        ];

        //构造MsgList
        $msg_list = [];
        $msg_list[] = $msg_list_elem;

        return $this->group_import_group_msg2($group_id, $msg_list);
    }

    /**
     * @inheritDoc
     */
    function group_import_group_msg2($group_id, $msg_list)
    {
        $msg = [
            "GroupId" => $group_id,
            "MsgList" => $msg_list,
        ];
        return $this->comm_rest('group_open_http_svc', 'import_group_msg', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function query_user_state($user_ids)
    {
        $msg = [
            'To_Account' => $user_ids,
        ];
        return $this->comm_rest('openim', 'querystate', json_encode($msg));
    }

    /**
     * @inheritDoc
     */
    function group_set_unread_msg_num($group_id, $member_account, $unread_msg_num)
    {
        $msg = [
            "GroupId" => $group_id,
            "Member_Account" => $member_account,
            "UnreadMsgNum" => (int)$unread_msg_num
        ];
        return $this->comm_rest('group_open_http_svc', 'set_unread_msg_num', json_encode($msg));
    }


    # REST API 访问接口集合

    public function openpic_pic_upload($account_id, $receiver, $pic_path, $busi_type)
    {
        #获取长度和md5值
        $pic_data = file_get_contents($pic_path);
        $md5 = md5($pic_data);
        $pic_size = filesize($pic_path);

        #进行base64处理
        $fp = fopen($pic_path, "r");
        $pic_data = fread($fp, $pic_size);

        $slice_data = array();
        $slice_size = array();
        $SLICE_SIZE = 32 * 4096;

        //对文件进行分片
        for ($i = 0; $i < $pic_size; $i = $i + $SLICE_SIZE) {
            if ($i + $SLICE_SIZE > $pic_size) {
                break;
            }
            $slice_tmp = substr($pic_data, $i, $SLICE_SIZE);
            $slice_tmp = chunk_split(base64_encode($slice_tmp));
            $slice_tmp = str_replace("\r\n", '', $slice_tmp);
            $slice_size[] = $SLICE_SIZE;
            $slice_data[] = $slice_tmp;
        }

        //最后一个分片
        if ($i - $SLICE_SIZE < $pic_size) {
            $slice_size[] = $pic_size - $i;
            $tmp = substr($pic_data, $i, $pic_size - $i);
            $slice_size[] = strlen($tmp);
            $tmp = chunk_split(base64_encode($tmp));
            $tmp = str_replace("\r\n", '', $tmp);

            $slice_data[] = $tmp;
        }

        $pic_rand = rand(1, 65535);
        $time_stamp = time();
        $req_data_list = array();
        $sentOut = 0;
        printf("handle %d segments\n", count($slice_data) - 1);
        for ($i = 0; $i < count($slice_data) - 1; $i++) {
            #构造消息
            $msg = array(
                "From_Account" => $account_id,  //发送者
                "To_Account" => $receiver,      //接收者
                "App_Version" => 1.4,       //应用版本号
                "Seq" => $i + 1,                      //同一个分片需要保持一致
                "Timestamp" => $time_stamp,         //同一张图片的不同分片需要保持一致
                "Random" => $pic_rand,              //同一张图片的不同分片需要保持一致
                "File_Str_Md5" => $md5,         //图片MD5，验证图片的完整性
                "File_Size" => $pic_size,       //图片原始大小
                "Busi_Id" => $busi_type,                    //群消息:1 c2c消息:2 个人头像：3 群头像：4
                "PkgFlag" => 1,                 //同一张图片要保持一致: 0表示图片数据没有被处理 ；1-表示图片经过base64编码，固定为1
                "Slice_Offset" => $i * $SLICE_SIZE,           //必须是4K的整数倍
                "Slice_Size" => $slice_size[$i],        //必须是4K的整数倍,除最后一个分片列外
                "Slice_Data" => $slice_data[$i]     //PkgFlag=1时，为base64编码
            );
            $req_data_list[] = $msg;
            $sentOut = 0;
            if ($i != 0 && ($i + 1) % 4 == 0) {
                //将消息序列化为json串
                $req_data_list = json_encode($req_data_list);
                printf("\ni = %d, call multi_api once\n", $i);
                $ret = $this->comm_rest_multi("openpic", "pic_up", $req_data_list);
                if (gettype($ret) == "string") {
                    $ret = json_decode($ret, true);
                    return $ret;
                }
                $req_data_list = array();
                $sentOut = 1;
            }
        }

        if ($sentOut == 0) {
            $req_data_list = json_encode($req_data_list);
            printf("\ni = %d, call multi_api once\n", $i);
            $this->comm_rest_multi("openpic", "pic_up", $req_data_list);
        }

        #最后一个分片
        $msg = array(
            "From_Account" => $account_id,    //发送者
            "To_Account" => $receiver,        //接收者
            "App_Version" => 1.4,        //应用版本号
            "Seq" => $i + 1,                        //同一个分片需要保持一致
            "Timestamp" => $time_stamp,            //同一张图片的不同分片需要保持一致
            "Random" => $pic_rand,                //同一张图片的不同分片需要保持一致
            "File_Str_Md5" => $md5,            //图片MD5，验证图片的完整性
            "File_Size" => $pic_size,        //图片原始大小
            "Busi_Id" => $busi_type,                    //群消息:1 c2c消息:2 个人头像：3 群头像：4
            "PkgFlag" => 1,                    //同一张图片要保持一致: 0表示图片数据没有被处理 ；1-表示图片经过base64编码，固定为1
            "Slice_Offset" => $i * $SLICE_SIZE,            //必须是4K的整数倍
            "Slice_Size" => $slice_size[count($slice_data) - 1],        //必须是4K的整数倍,除最后一个分片列外
            "Slice_Data" => $slice_data[count($slice_data) - 1]        //PkgFlag=1时，为base64编码
        );

        $req_data = json_encode($msg);
        return $this->comm_rest("openpic", "pic_up", $req_data);
    }

    /**
     * @inheritDoc
     */
    function comm_rest($server_name, $command, $req_body)
    {
        $url = sprintf(self::REQUEST_URL, $server_name, $command, $this->usersig, $this->identifier, $this->sdkappid);
        $ret = $this->http_req('https', 'post', $url, $req_body);
        return json_decode($ret, true);
    }

    /**
     * @inheritDoc
     */
    function comm_rest_multi($server_name, $command, $req_body)
    {
        $url = sprintf(self::REQUEST_URL, $server_name, $command, $this->usersig, $this->identifier, $this->sdkappid);
        $ret = $this->http_req_multi('https', 'post', $url, $req_body);
        return json_decode($ret, true);
    }

    /**
     * 向Rest服务器发送请求
     * @param string $http_type http类型,比如https
     * @param string $method 请求方式，比如POST
     * @param string $url 请求的url
     * @return string $data 请求的数据
     */
    public static function http_req($http_type, $method, $url, $data)
    {
        $ch = curl_init();
        if (strstr($http_type, 'https')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $url = $url . '?' . $data;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100000);//超时时间

        try {
            $ret = curl_exec($ch);
        } catch (\Exception $e) {
            curl_close($ch);
            return json_encode(array('ret' => 0, 'msg' => 'failure'));
        }
        curl_close($ch);
        return $ret;
    }

    /**
     * 向Rest服务器发送多个请求(并发)
     * @param string $http_type http类型,比如https
     * @param string $method 请求方式，比如POST
     * @param string $url 请求的url
     * @return bool 是否成功
     */
    public static function http_req_multi($http_type, $method, $url, $data)
    {
        $mh = curl_multi_init();
        $ch_list = array();
        $i = -1;
        $req_list = array();
        foreach ($data as $req_data) {
            $i++;
            $req_data = json_encode($req_data);
            $ch = curl_init();
            if ($http_type == 'https://') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            }

            if ($method == 'post') {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);
            } else {
                $url = $url . '?' . $data;
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 100000);//超时时间
            curl_multi_add_handle($mh, $ch);
            $ch_list[] = $ch;
            $req_list[] = $req_data;
        }
        try {
            do {
                $mret = curl_multi_exec($mh, $active);
            } while ($mret == CURLM_CALL_MULTI_PERFORM);

            while ($active and $mret == CURLM_OK) {
                if (curl_multi_select($mh) === -1) {
                    usleep(100);
                }
                do {
                    $mret = curl_multi_exec($mh, $active);
                } while ($mret == CURLM_CALL_MULTI_PERFORM);
            }
        } catch (\Exception $e) {
            curl_close($ch);
            return json_encode(array('ret' => 0, 'msg' => 'failure'));
        }
        for ($i = 0; $i < count($ch_list); $i++) {
            $ret = curl_multi_getcontent($ch_list[$i]);
            if (strstr($ret, "URL_INFO")) {
                curl_multi_close($mh);
                return $ret;
            }
            $ret = json_decode($ret, true);
            echo json_format($ret);
        }
        curl_multi_close($mh);
        return true;
    }
}

//辅助过滤器类
class Filter
{
}

;

/** Json数据格式化方法
 * @param array $data 数组数据
 * @param string $indent 缩进字符，默认4个空格
 * @return string json格式字符串
 */
function json_format($data, $indent = null)
{
    // 对数组中每个元素递归进行urlencode操作，保护中文字符
    array_walk_recursive($data, 'json_format_protect');

    // json encode
    $data = json_encode($data);

    // 将urlencode的内容进行urldecode
    $data = urldecode($data);

    // 缩进处理
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent) ? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;
    for ($i = 0; $i <= $length; $i++) {
        $char = substr($data, $i, 1);
        if ($char == '"' && $prevchar != '\\') {
            $outofquotes = !$outofquotes;
        } elseif (($char == '}' || $char == ']') && $outofquotes) {
            $ret .= $newline;
            $pos--;
            for ($j = 0; $j < $pos; $j++) {
                $ret .= $indent;
            }
        }
        $ret .= $char;
        if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
            $ret .= $newline;
            if ($char == '{' || $char == '[') {
                $pos++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $ret .= $indent;
            }
        }
        $prevchar = $char;
    }
    return $ret;
}

/**
 * json_formart辅助函数
 * @param String $val 数组元素
 */
function json_format_protect(&$val)
{
    if ($val !== true && $val !== false && $val !== null) {
        $val = urlencode($val);
    }
}

/**
 * 判断操作系统位数
 */
function is_64bit()
{
    $int = "9223372036854775807";
    $int = intval($int);
    if ($int == 9223372036854775807) {
        /* 64bit */
        return true;
    } elseif ($int == 2147483647) {
        /* 32bit */
        return false;
    } else {
        /* error */
        return "error";
    }
}
