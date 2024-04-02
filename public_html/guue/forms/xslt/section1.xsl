<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- 
####################################################################################
#  XSL name : section1
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                   
#  Last update : 08/06/2018                                                      
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="*:DOCUMENT_FULL|*:DOCUMENT_RESTRICTED|*:URL_DOCUMENT|*:ADDRESS_FURTHER_INFO_IDEM|*:ADDRESS_FURTHER_INFO|*:URL_PARTICIPATION|*:ADDRESS_PARTICIPATION|*:ADDRESS_PARTICIPATION_IDEM|*:URL_TOOL" mode="R300">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F21' and @FORM!='F22'  and @FORM!='F23']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='AWARD_CONTRACT' and @TYPE!='CONCESSION_AWARD_CONTRACT']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R300'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:DOCUMENT_FULL|*:DOCUMENT_RESTRICTED|*:URL_DOCUMENT" mode="R301R302R303">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F01' and @FORM!='F04' and @FORM!='F07']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='PRI_ONLY' and @TYPE!='PER_ONLY']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE='QSU_CALL_COMPETITION']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:variable name="ele2" select="local-name(.)"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM='F01']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R301'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM='F04']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R302'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM='F07']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R303'"/>
							<xsl:with-param name="content" select="$ele2"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][*:NOTICE[@TYPE!='AWARD_CONTRACT' and @TYPE!='CONCESSION_AWARD_CONTRACT']]" mode="R304">
		<xsl:choose>
			<xsl:when test="@FORM!='F21' and @FORM!='F22'  and @FORM!='F23'"/>
			<xsl:when test=".//*:ADDRESS_FURTHER_INFO_IDEM|.//*:ADDRESS_FURTHER_INFO"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R304'"/>
					<xsl:with-param name="content" select="concat('NOTICE/@TYPE=',*:NOTICE/@TYPE,' and ', 'missing ADDRESS_FURTHER_INFO(_IDEM)')"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:URL_PARTICIPATION|*:ADDRESS_PARTICIPATION|*:ADDRESS_PARTICIPATION_IDEM" mode="R305R306">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM!='F01' and @FORM!='F04' and @FORM!='F21' and @FORM!='F22']"/>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE[@TYPE!='PRI_ONLY' and @TYPE!='PER_ONLY']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=', ancestor::*[@FORM]//*:NOTICE/@*,' and ',local-name(.))"/>
				<xsl:choose>
					<xsl:when test="ancestor::*[@FORM='F01' or @FORM='F21']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R305'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test="ancestor::*[@FORM='F04' or @FORM='F22']">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R306'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][*:NOTICE[@TYPE!='PRI_ONLY' and @TYPE!='PER_ONLY' and @TYPE!='AWARD_CONTRACT' and @TYPE!='CONCESSION_AWARD_CONTRACT']]" mode="R307R308R309R310">
		<xsl:choose>
			<xsl:when test="@FORM!='F01' and @FORM!='F04' and @FORM!='F21' and @FORM!='F22' and @FORM!='F23'"/>
			<xsl:when test=".//*:URL_PARTICIPATION|.//*:ADDRESS_PARTICIPATION|.//*:ADDRESS_PARTICIPATION_IDEM"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('NOTICE/@TYPE=',*:NOTICE/@TYPE,' and ', 'missing {URL_PARTICIPATION,ADDRESS_PARTICIPATION(_IDEM)}')"/>
				<xsl:choose>
					<xsl:when test=".//@TYPE='PRI_REDUCING_TIME_LIMITS'">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R307'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test=".//@TYPE='PRI_CALL_COMPETITION' or .//@TYPE='PER_CALL_COMPETITION' or .//@TYPE='CONTRACT'">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R308'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test=".//@TYPE='QSU_ONLY' or .//@TYPE='QSU_CALL_COMPETITION'">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R309'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:when test=".//@TYPE='PRI'">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R310'"/>
							<xsl:with-param name="content" select="$ele"/>
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:CA_TYPE|*:CA_TYPE_OTHER" mode="R311">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]//*:LEGAL_BASIS) or ancestor::*[@FORM]//*:LEGAL_BASIS/@*!='32014L0025'"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R311'"/>
					<xsl:with-param name="content" select="concat('LEGAL_BASIS/@VALUE=',ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and ',local-name(.))"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:CA_ACTIVITY|*:CA_ACTIVITY_OTHER" mode="R312">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]//*:LEGAL_BASIS) or ancestor::*[@FORM]//*:LEGAL_BASIS/@*!='32014L0025'"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R312'"/>
					<xsl:with-param name="content" select="concat('LEGAL_BASIS/@VALUE=', ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and ',local-name(.))"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:CE_ACTIVITY|*:CE_ACTIVITY_OTHER" mode="R313">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM]//*:LEGAL_BASIS) or ancestor::*[@FORM]//*:LEGAL_BASIS/@*!='32014L0024'"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R313'"/>
					<xsl:with-param name="content" select="concat('LEGAL_BASIS/@VALUE=', ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and ',local-name(.))"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:CA_TYPE[@*!='EU_INSTITUTION']|*:CA_TYPE_OTHER" mode="R381">
		<xsl:choose>
			<xsl:when test="not(ancestor::*[@FORM='F01' or @FORM='F02' or @FORM='F03'])"/>
			<xsl:when test="ancestor::*[@FORM]//*:LEGAL_BASIS/@*!='32012R0966'"/>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="local-name()='CA_TYPE'">
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R381'"/>
							<xsl:with-param name="content" select="concat('LEGAL_BASIS/@VALUE=',ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and CA_TYPE/@VALUE=',@*)"/>
						</xsl:call-template>
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="msg">
							<xsl:with-param name="rule" select="'R381'"/>
							<xsl:with-param name="content" select="concat('LEGAL_BASIS/@VALUE=',ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and CA_TYPE_OTHER')"/>
						</xsl:call-template>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='T01' or @FORM='T02'][.//*:CA_TYPE/@*='EU_INSTITUTION']" mode="R386">
		<xsl:variable name="ele" select="concat(@FORM,' form and CA_TYPE/@VALUE=EU_INSTITUTION')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R386'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM='F14']//*:CONTRACTING_BODY/*[not(*:TOWN and *:COUNTRY and *:E_MAIL and *:NUTS and *:URL_GENERAL)]" mode="R387">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:LEGAL_BASIS[@*='32007R1370' or @*='32004L0017' or @*='32004L0018' or @*='32009L0081']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('LEGAL_BASIS[@VALUE=',ancestor::*[@FORM]//*:LEGAL_BASIS[@*!='32007R1370'][1]/@*,'] and ',local-name(),'[missing {TOWN, COUNTRY, E_MAIL, NUTS, URL_GENERAL}]')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R387'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
