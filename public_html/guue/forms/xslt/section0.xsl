<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : section0
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                      
#  Last update : 08/06/2018                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0" xmlns:mi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ted="http://publications.europa.eu/resource/schema/ted/R2.0.9/reception" exclude-result-prefixes="ted">
	<!---->
	<xsl:template match="@mi:schemaLocation" mode="R998">
		<xsl:if test="not(matches(normalize-space(string()),'^([^\s]+&#x20;[^\s]+\.xsd(&#x20;[^\s]+&#x20;[^\s]+\.xsd)*)$'))">
			<xsl:variable name="attr" select="concat('xsi:schemaLocation with value ', &quot;'&quot;, string(), &quot;'&quot;)"/>
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R998'"/>
				<xsl:with-param name="content" select="$attr"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*" mode="R100">
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R100'"/>
			<xsl:with-param name="content" select="''"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:FORM_SECTION" mode="R101">
		<xsl:if test=".//*[@CURRENCY != .//*/@CURRENCY[1]]">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R101'"/>
				<xsl:with-param name="content" select="''"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*:E_MAIL" mode="R102">
		<xsl:variable name="ele">'<xsl:value-of select="normalize-space(text())"/>'</xsl:variable>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R102'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM]" mode="R104">
		<xsl:if test=".//*:OBJECT_CONTRACT[position()!=@ITEM] ">
			<xsl:variable name="ele" select="'OBJECT_CONTRACT/@ITEM'"/>
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R104'"/>
				<xsl:with-param name="content" select="$ele"/>
			</xsl:call-template>
		</xsl:if>
		<xsl:if test=".//*:OBJECT_CONTRACT[child::*:OBJECT_DESCR[position()!=@ITEM]] ">
			<xsl:variable name="ele" select="'OBJECT_DESCR/@ITEM'"/>
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R104'"/>
				<xsl:with-param name="content" select="$ele"/>
			</xsl:call-template>
		</xsl:if>
		<xsl:if test=".//*:AWARD_CONTRACT[position()!=@ITEM]">
			<xsl:variable name="ele" select="'AWARD_CONTRACT/@ITEM'"/>
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R104'"/>
				<xsl:with-param name="content" select="$ele"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*:USER_E_MAIL|*:E_MAIL" mode="R105">
		<xsl:if test="(string-length(normalize-space(.))&gt; 200  ) or (string-length(normalize-space(.))&gt; 0 and not(matches(normalize-space(.),'^[A-Za-z0-9!#$%&amp;''*+/=?_-]+(\.[A-Za-z0-9!#$%&amp;''*+/=?_-]+)*@([A-Za-z0-9]([A-Za-z0-9_-]*[A-Za-z0-9])?\.)+([A-Za-z]{2,})$')))">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R105'"/>
				<xsl:with-param name="content" select="concat(local-name(.),' with value ', &quot;'&quot;, normalize-space(./text()), &quot;'&quot;)"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*[*:DATE_END/number(replace(text(),'-','')) &lt;= *:DATE_START/number(replace(text(),'-',''))]" mode="R106">
		<xsl:variable name="ele" select="concat(local-name(parent::*),'[',parent::*/@ITEM,']/',local-name(),'[',@ITEM,'][DATE_START=',*:DATE_START/text(),', ','DATE_END=',*:DATE_END/text(),']')"/>
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R106'"/>
			<xsl:with-param name="content" select="$ele"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*:NOTICE_NUMBER_OJ" mode="R107">
		<xsl:choose>
			<xsl:when test="matches(./text(),'^(20|19)\d{2}/S (((00)?[1-9])|([0]?[1-9][0-9])|(1[0-9][0-9])|(2[0-5][0-9]))-\d{6}$')"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R107'"/>
					<xsl:with-param name="content" select="concat(local-name(.),' with value ',current())"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM]" mode="R115">
		<xsl:call-template name="msg">
			<xsl:with-param name="rule" select="'R115'"/>
			<xsl:with-param name="content" select="concat('Total CPV: ',count(.//*[matches(local-name(),'CPV')][attribute::CODE]/@CODE),' / Unique CPV: ',count(distinct-values(.//*[matches(local-name(),'CPV')][attribute::CODE]/@CODE)))"/>
		</xsl:call-template>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM]" mode="R116">
		<xsl:if test=".//*:LOT_DIVISION">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R116'"/>
				<xsl:with-param name="content" select="concat('Number of LOT: ',count(.//*:OBJECT_DESCR))"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*:VAL_RANGE_OBJECT|*:VAL_RANGE_TOTAL" mode="R158">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'AWARD'))]"/>
			<xsl:when test="number(*:LOW/text())>0 and number(*:HIGH/text())&gt;=number(*:LOW/text())"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R158'"/>
					<xsl:with-param name="content" select="concat(local-name(),'[HIGH=',string(./*:HIGH),', LOW=',string(./*:LOW),']')"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:DATE_DISPATCH_NOTICE" mode="R103">
		<xsl:choose>
			<xsl:when test="number(substring-before(//*:IDENTIFICATION/*:NO_DOC_EXT/text(),'-'))=number(substring-before(./text(),'-'))"/>
			<xsl:otherwise>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R103'"/>
					<xsl:with-param name="content" select="concat('NO_DOC_EXT=',&quot;'&quot;,//*:IDENTIFICATION/*:NO_DOC_EXT/text(),&quot;'&quot;,', DATE_DISPATCH_NOTICE=',&quot;'&quot;,text(),&quot;'&quot;)"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*:FORM_SECTION//*:NO_DOC_EXT[compare(./text(),//*:SENDER//*:NO_DOC_EXT/text())=0]" mode="R113">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM]//*:NOTICE/@*[not(matches(.,'AWARD'))]"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(local-name(ancestor::*:F14_2014|ancestor::*:AWARD_CONTRACT),'[',ancestor::*:AWARD_CONTRACT/@ITEM,']//NO_DOC_EXT=',./text(),', SENDER/IDENTIFICATION/NO_DOC_EXT=',//*:SENDER//*:NO_DOC_EXT/text())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R113'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM]" mode="R117">
		<xsl:variable name="error_nb">
			<xsl:value-of select="count(.//*[*:TYPE_CONTRACT/@*='SUPPLIES']//*:CPV_CODE[not(45>number(substring(@*,1,2)) or number(substring(@*,1,2))=48)])
			+ count(.//*[*:TYPE_CONTRACT/@*='WORKS']//*:CPV_CODE[number(substring(@*,1,2))!=45])
			+ count(.//*[*:TYPE_CONTRACT/@*='SERVICES']//*:CPV_CODE[49>number(substring(@*,1,2)) or number(substring(@*,1,2))>98])"/>
		</xsl:variable>
		<xsl:if test="$error_nb>0">
			<xsl:call-template name="msg">
				<xsl:with-param name="rule" select="'R117'"/>
				<xsl:with-param name="content" select="concat('Found ',string($error_nb),' incorrect CPV')"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!---->
	<xsl:template match="*:LEGAL_BASIS" mode="R118">
		<xsl:choose>
			<xsl:when test="ancestor::*[@FORM='F20'] and @*='32004L0017'"/>
			<xsl:when test="ancestor::*[@FORM='F20'] and @*='32004L0018'"/>
			<xsl:when test="ancestor::*[@FORM='F08' or @FORM='F15' or @FORM='F20'] and @*='32009L0081'"/>
			<xsl:when test="ancestor::*[@FORM='F20' or @FORM='T01' or @FORM='T02'] and @*='32007R1370'"/>
			<xsl:when test="ancestor::*[ @FORM='F15' or @FORM='F20' or @FORM='F23' or @FORM='F24' or @FORM='F25'] and @*='32014L0023'"/>
			<xsl:when test="ancestor::*[@FORM='F01' or @FORM='F02' or @FORM='F03' or @FORM='F08' or @FORM='F12'or @FORM='F13' or @FORM='F15' or @FORM='F20' or @FORM='F21'] and @*='32014L0024'"/>
			<xsl:when test="ancestor::*[@FORM='F04' or @FORM='F05' or @FORM='F06' or @FORM='F07' or @FORM='F08' or @FORM='F12'or @FORM='F13' or @FORM='F15' or @FORM='F20' or @FORM='F22'] and @*='32014L0025'"/>
			<xsl:when test="ancestor::*[@FORM='D01' or @FORM='D02' or @FORM='D03' or @FORM='D20']  and @*='32015R0323'"/>
			<xsl:when test="ancestor::*[@FORM='F01' or @FORM='F02' or @FORM='F03' or @FORM='F20' or @FORM='D01' or @FORM='D02' or @FORM='D03' or @FORM='D20'] and @*='32012R0966'"/>
			<xsl:when test="ancestor::*[@FORM='F14'] and (@*='32012R0966' or @*='32015R0323') and //*:SENDER//*:ESENDER_LOGIN[text()='TEDDEVCO']"/>
			<xsl:when test="ancestor::*[@FORM='F14'] and (@*='32004L0017' or @*='32004L0018' or @*='32009L0081' or @*='32007R1370' or  @*='32014L0023' or @*='32014L0024' or @*='32014L0025' or @*='32012R0966' ) and //*:SENDER//*:ESENDER_LOGIN[text()!='TEDDEVCO']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(ancestor::*[@FORM]/@FORM,' form and LEGAL_BASIS[@VALUE=',@*,']')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R118'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="*:LEGAL_BASIS_OTHER" mode="R118">
		<xsl:choose>
			<xsl:when test="not(descendant::*[matches(text()[1],'[\p{L}\p{N}]+')]) and preceding-sibling::*:LEGAL_BASIS"/>
			<xsl:when test="descendant::*[matches(text()[1],'[\p{L}\p{N}]+')] and //*:SENDER//*:ESENDER_LOGIN[text()='TEDDEVCO']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(ancestor::*[@FORM]/@FORM,' form and LEGAL_BASIS_OTHER')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R118'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template mode="R119" match="*:OBJECT_CONTRACT//*:VAL_TOTAL|
	*:OBJECT_CONTRACT//*:VAL_RANGE_TOTAL|
	*:AC|
	*:TENDERS|
	*:CONTRACTORS|
	*:VALUES|*:PARTICIPANTS|
	*:WINNERS|
	*:VAL_PRIZE|
	*[@FORM='F13']//*:CRITERIA_EVALUATION">
		<xsl:choose>
			<xsl:when test="not(//@FORM='F06' or //@FORM='F13'  or //@FORM='F15' or //@FORM='F22') "/>
			<xsl:when test="not(@PUBLICATION) and ancestor::*[@FORM]//*:LEGAL_BASIS/@*!='32014L0025'"/>
			<xsl:when test="@PUBLICATION and ancestor::*[@FORM]//*:LEGAL_BASIS/@*='32014L0025'"/>
			<xsl:otherwise>
				<xsl:if test="not(@PUBLICATION)">
					<xsl:variable name="ele" select="concat('LEGAL_BASIS/@VALUE=',ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and ', parent::*/local-name(), '/', local-name(),'[missing @PUBLICATION]')"/>
					<xsl:call-template name="msg">
						<xsl:with-param name="rule" select="'R119'"/>
						<xsl:with-param name="content" select="$ele"/>
					</xsl:call-template>
				</xsl:if>
				<xsl:if test="@PUBLICATION">
					<xsl:variable name="ele" select="concat('LEGAL_BASIS/@VALUE=',ancestor::*[@FORM]//*:LEGAL_BASIS/@*,' and ', parent::*/local-name(),'/',local-name(),'[@PUBLICATION]')"/>
					<xsl:call-template name="msg">
						<xsl:with-param name="rule" select="'R119'"/>
						<xsl:with-param name="content" select="$ele"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][//*:DATE_EXPECTED_PUBLICATION]" mode="R120">
		<xsl:choose>
			<xsl:when test="(@FORM='D01' or @FORM='D02'  or @FORM='D03' or @FORM='D20' or @FORM='F14') and //*:SENDER//*:ESENDER_LOGIN[text()='TEDDEVCO']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(@FORM,' form and DATE_EXPECTED_PUBLICATION')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R120'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][//*:SENDER//*:ESENDER_LOGIN[text()='TEDDEVCO'] and //*:DATE_EXPECTED_PUBLICATION]" mode="R121">
		<xsl:choose>
			<xsl:when test="@FORM!='D01' and @FORM!='D02'  and @FORM!='D03' and @FORM!='D20' and @FORM!='F14'"/>
			<xsl:when test="number(replace(//*:DATE_EXPECTED_PUBLICATION/text(),'-','')) &gt; (6+ number(replace(.//*:DATE_DISPATCH_NOTICE/text(),'-','')))"/>
			<xsl:when test="@FORM='F14' and (number(replace(//*:DATE_EXPECTED_PUBLICATION/text(),'-','')) &gt; (4+ number(replace(.//*:DATE_DISPATCH_NOTICE/text(),'-',''))))"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat('DATE_EXPECTED_PUBLICATION=',//*:DATE_EXPECTED_PUBLICATION/text(),', DATE_DISPATCH_NOTICE=',.//*:DATE_DISPATCH_NOTICE/text())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R121'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM][//*:SENDER//*:ESENDER_LOGIN[text()='TEDDEVCO'] and not(//*:DATE_EXPECTED_PUBLICATION)]" mode="R122">
		<xsl:choose>
			<xsl:when test="@FORM!='D01' and @FORM!='D02'  and @FORM!='D03' and @FORM!='D20' and @FORM!='F14'"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(@FORM,' form and missing DATE_EXPECTED_PUBLICATION')"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R122'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!---->
	<xsl:template match="*[@FORM]" mode="R123">
		<xsl:choose>
			<xsl:when test="(@FORM='D01' or @FORM='D02'  or @FORM='D03' or @FORM='D20' or @FORM='F14') and //*:SENDER//*:ESENDER_LOGIN[text()='TEDDEVCO']"/>
			<xsl:when test="(@FORM!='D01' and @FORM!='D02'  and @FORM!='D03' and @FORM!='D20') and //*:SENDER//*:ESENDER_LOGIN[text()!='TEDDEVCO']"/>
			<xsl:otherwise>
				<xsl:variable name="ele" select="concat(@FORM,' form and ESENDER_LOGIN=',//*:SENDER//*:ESENDER_LOGIN/text())"/>
				<xsl:call-template name="msg">
					<xsl:with-param name="rule" select="'R123'"/>
					<xsl:with-param name="content" select="$ele"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
