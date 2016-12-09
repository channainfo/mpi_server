<?php
/**
 * Define the constant of the application
 * @author Sokha RUM
 */
class Iconstant {
	/** application name */
    const MPI_APP_NAME = "Master Patient Index Server";
    
    /** the date format to display */
    const APP_DATE_FORMAT = "D/M/Y";
    
    /** 
     * the list of fingerprint order by its priority
     * 		 r --> right hand
     *       l --> left hand
     *       1 --> thumb finger
     *       2 --> grooming finger
     *       3 --> middle finger
     *       4 --> ring finger
     *       5 --> little finger
     *  Example: fingerprint_r1 means the thumb of right hand
     */
    public static $MPI_FINGERPRINT = array(
    							 "fingerprint_r1",
    							 "fingerprint_l1",
    							 "fingerprint_r2",
        						 "fingerprint_l2",
        						 "fingerprint_r3",
    							 "fingerprint_l3",
    							 "fingerprint_r4",
    							 "fingerprint_l4",
    							 "fingerprint_r5",
    							 "fingerprint_l5");
    
    
    /** 
     * 
     * 		 r --> right hand
     *       l --> left hand
     *       1 --> thumb finger
     *       2 --> grooming finger
     *       3 --> middle finger
     *       4 --> ring finger
     *       5 --> little finger
     *  Example: user_r1 means the thumb of right hand
     */
    public static $MPI_USER_FP = array(
    							 "member_fp_r1",
    							 "member_fp_l1",
    							 "member_fp_r2",
        						 "member_fp_l2",
        						 "member_fp_r3",
    							 "member_fp_l3",
    							 "member_fp_r4",
    							 "member_fp_l4",
    							 "member_fp_r5",
    							 "member_fp_l5");
    
    
    /**
     * Services which are currently available for MPI
     * 		1 --> VCCT
     * 		2 --> OI/ART
     * 		3 --> STD
     */
    public static $MPI_SERVICE = array(1, 2, 3);
    
    /** row per page for pagination*/
    const PAGINATION_ROW_PER_PAGE = 15;
    
    /** User level admin */
    const USER_ADMIN = 1;
    /** Normal user level */
    const USER_NORMAL = 2;
}